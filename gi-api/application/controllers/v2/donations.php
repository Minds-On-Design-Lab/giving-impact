<?php
require_once(APPPATH.'/libraries/stripe/Stripe.php');

/**
 * Donation controller
 *
 * @class Donations
 * @extends CI_Controller
 */
class Donations extends CI_Controller {

    private $required = array(
        'first_name',
        'last_name',
        'billing_address1',
        'billing_city',
        // 'billing_state',
        'billing_postal_code',
        'billing_country',
        'email_address',
        'contact'
    );

    private $allowed = array(
        'first_name',
        'last_name',
        'billing_address1',
        'billing_city',
        'billing_state',
        'billing_postal_code',
        'billing_country',
        'donation_total',
        'donation_level',
        'donation_date',
        'email_address',
        'contact',
        'card',
        'custom_responses',
        'donation_level_id'
    );

    public function __construct() {
        parent::__construct();

        $this->load->model('Campaign_model');
        $this->load->model('Account_model');
        $this->load->model('Opportunity_model');
        $this->load->model('Donation_model');
        $this->load->model('Task_model');
        $this->load->model('Hook_model');
        $this->load->model('Supporter_model');
        $this->load->model('Transaction_model');

        $this->load->helper('number');
        $this->load->helper('display');
        $this->load->helper('api');
        $this->load->helper('notification');

        if( $this->authorization->get_account()->currency == 'usd' ) {
            $this->required[] = 'billing_state';
        }
    }

    /**
     * Handles creation of donation, expects either campaign
     * or opportunity HASH to be sent in POST data
     *
     * Handles
     *
     *  * POST /api/v2/donations
     *
     * Accepts via POST:
     *
     *  * first_name {String}
     *  * last_name {String}
     *  * billing_address1 {String}
     *  * billing_city {String}
     *  * billing_state {String}
     *  * billing_postal_code {String}
     *  * billing_country {String}
     *  * donation_total {Float}
     *  * donation_level {String}
     *  * donation_level_id {Int}
     *  * donation_date {String}
     *  * email_address {String}
     *  * contact {Boolean}
     *
     * @return Array of Donation_model objects
     */
    public function index() {
        if( !$this->input->post_body ) {
//          $this->output->show_error('No request data found');
//          return;
            if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
                $this->output->show_error('Your request body is missing or malformed', 400, 'bad_request');
                return;
            }

            if( $this->input->get('supporter') ) {

                $id = $this->input->get('supporter');
                if( strpos($id, '@') !== false ) {
                    $supporter = $this->Supporter_model
                        ->where('email_address', $id)
                        ->where('account_id', $this->authorization->account)
                        ->limit(1)
                        ->find();
                } else {
                    $supporter = $this->Supporter_model
                        ->where('supporter_token', $id)
                        ->where('account_id', $this->authorization->account)
                        ->limit(1)
                        ->find();
                }

                if( count($supporter) ) {
                    $supporter = $supporter[0];

                    $donations = api_limit_sort($this->Donation_model)
                        ->select_complete()
                        ->where('supporter_id', $supporter->id)
                        ->where('donations.account_id', $this->authorization->account)
                        ->find();

                    return $this->load->view('v2/donations/index', array(
                        'donations' => prep_donations($donations)
                    ));
                }

                $this->output->show_error('No supporter found', 400, 'missing_supporter');
                return;

            } else {
                $donations = api_limit_sort($this->Donation_model)
                    ->select_complete()
                    ->where('donations.account_id', $this->authorization->account)
                    ->find();

                $this->load->view('v2/campaigns/donations', array(
                    'donations' => prep_donations($donations)
                ));
            }

            return;
        }

        $acct = $this->Account_model
            ->where('id', $this->authorization->account)
            ->find();
        if( !$acct[0]->currency ) {
            $this->output->show_error('This campaign is not accepting donations', 400, 'bad_request');
            return;
        }

        $data = $this->input->post_body;
        $campaign = false;
        $opportunity = false;
        if( property_exists($this->input->post_body, 'campaign')
            && strlen($this->input->post_body->campaign)
        ) {
            $campaign = $this->input->post_body->campaign;
        } elseif( property_exists($this->input->post_body, 'opportunity')
            && strlen($this->input->post_body->opportunity)
        ) {
            $opportunity = $this->input->post_body->opportunity;
        }

        if( !$campaign && !$opportunity ) {
            $this->output->show_error('Campaign or opportunity token is required');
            return;
        }

        if( $campaign ) {
            $campaigns = $this->Campaign_model
                ->where('account_id', $this->authorization->account)
                ->where('campaign_token', $campaign)
                ->where('campaign_id', NULL)
                ->find();

            if( !count($campaigns) ) {
                $this->log->entry('Invalid campaign token');
                $this->output->show_error('Campaign not found', 404, 'invalid_token');
                return;
            }

            $campaign = $campaigns[0];
            $opportunity = false;
        } else {
            $opportunities = $this->Opportunity_model
                ->where('account_id', $this->authorization->account)
                ->where('campaign_token', $opportunity)
                ->find();

            if( !count($opportunities) ) {
                $this->log->entry('Invalid opportunity token');
                $this->output->show_error('Opportunity not found', 404, 'invalid_token');
                return;
            }

            $opportunity = $opportunities[0];
            $campaign = $opportunity->campaign;
        }

        $donation = $this->_process_post(new Donation_model);
        if( !$donation ) {
            return;
        }

        $donation = $this->_process_custom_responses($donation, $campaign);
        if( !$donation ) {
            return;
        }

        $matched_donation_level = false;
        $current_donation_amount = false;
        if( $donation->donation_level_id ) {
            $matched_donation_level =  $this->_find_donation_level($donation->donation_level_id,$campaign->donation_levels);
        }
        if( $donation->amount ) {
            $current_donation_amount = $donation->amount;
        }

        // no email address
        if( !$donation->email_address ) {
            $this->output->show_error('Email address is a required field', 400, 'invalid_email');
        }

        // sent a level that doesn't go with this campaign
        if( $donation->donation_level_id && !$matched_donation_level ) {
            $this->output->show_error('The donation level provided does not belong to this campaign', 400, 'invalid_donation');
            return;
        }

        // sent a level but not an amount
        if( $matched_donation_level && !$current_donation_amount ) {

            $donation->amount = $matched_donation_level->amount;
            $donation->description = $matched_donation_level->description;

        // sent level and an amount, assume the amount is the charge total
        } elseif( $matched_donation_level && $current_donation_amount ) {

            $donation->amount = $current_donation_amount;
            $donation->donation_level_id = $donation->donation_level_id;
            $donation->description = $matched_donation_level->description;

        // did not send a level, but did send an amount
        } elseif( !$matched_donation_level && $current_donation_amount ) {

            $donation->donation_level_id = false;
            $donation->amount = $current_donation_amount;

        // sent neither
        } elseif( !$matched_donation_level && !$current_donation_amount ) {

            $this->output->show_error('You must provide a donation level and/or donation amount', 400, 'invalid_donation');
            return;

        }

        if( $donation->amount != floor($donation->amount) || !floor($donation->amount) ) {
            $this->log->entry('Invalid donation amount');
            $this->output->show_error('Donations must be in whole dollar amounts', 400, 'invalid_donation');
            return;
        }

        if( ($campaign->minimum_donation_amount && $donation->amount < $campaign->minimum_donation_amount)
            || (!$campaign->minimum_donation_amount && $donation->amount < 5) ) {

            $min = $campaign->minimum_donation_amount ? $campaign->minimum_donation_amount : 5;
            $this->log->entry('Invalid donation amount');
            $this->output->show_error('Donations must greater than or equal to '.money_format('%n', $min/100), 400, 'invalid_donation');
            return;
        }

        $donation->campaign_id = $opportunity ? $opportunity->id : $campaign->id;
        $donation->campaign_group_id = $campaign->id;

        $donation->account_id = $this->authorization->account;

        try {
            if( property_exists($this->input->post_body, 'card') && $this->input->post_body->card ) {

                $charge = Stripe_Charge::create(
                    array(
                        'amount'    => $donation->amount * 100,
                        'currency'  => $this->authorization->get_account()->currency,
                        'card'      => $this->input->post_body->card,
                        'description' => $donation->email_address
                    ),
                    $this->config->item('stripe_secret_key')
                );

                $donation->complete = 1;
                $donation->stripe_charge_id = $charge->id;
                $donation->offline = false;
            }
        } catch( Exception $e ) {
            $this->log->entry('Could not process card: '.$e->getMessage());
            $this->output->show_error($e->getMessage(), 404, 'processing_error');
            return;
        }

        // update supporter model
        $supporter = $this->Supporter_model
            ->where('account_id', $this->authorization->account)
            ->where('email_address', $donation->email_address)
            ->find();

        if( !$supporter || !count($supporter) ) {
            $supporter = new Supporter_model;
            $supporter->account_id          = $this->authorization->account;
            $supporter->email_address       = $donation->email_address;
            $supporter->first_name          = $donation->first_name;
            $supporter->last_name           = $donation->last_name;
            $supporter->street_address      = $donation->billing_address1;
            $supporter->city                = $donation->billing_city;
            $supporter->state               = $donation->billing_state;
            $supporter->postal_code         = $donation->billing_postal_code;
            $supporter->country             = $donation->billing_country;

            $supporter->save_entry();
        } else {
            $supporter = $supporter[0];
        }

        $donation->supporter_id = $supporter->id;

        $donation->save_entry();
        $supporter->save_entry();

        // check for hooks and generate a task call
        $hooks = $this->Hook_model
            ->where('account_id', $this->authorization->account)
            ->where('status', true)
            ->where('event', 'donation.create')
            ->find();
        foreach( $hooks as $hook ) {
            $task = $this->Task_model
                ->createTask($hook, $donation);
            $task->save_entry();
        }

        // just make sure we have all the right data
        $donation = $this->Donation_model
            ->select_complete()
            ->get_entry($donation->id);

        // make sure to update parent(s)
        if( $opportunity ) {
            $opportunity->current = $opportunity->current + $donation->amount;
            $opportunity->total_donations = $opportunity->total_donations + 1;
            $opportunity->save_entry();
        }
        $campaign->current = $campaign->current + $donation->amount;
        $campaign->total_donations = $campaign->total_donations + 1;
        $campaign->save_entry();

        if( property_exists($this->input->post_body, 'card') && isset($charge) && $charge->id ) {
            $txn = new Transaction_model;
            $txn->donation_id         = $donation->id;
            $txn->stripe_id           = $charge->id;
            $txn->type                = 'charge';
            $txn->total               = $donation->amount;
            $txn->save_entry();
        }

        if( $campaign->send_receipt && !$donation->offline ) {
            $c = $opportunity ? $opportunity : $campaign;
            notify_receipt($donation, $c, $this->authorization->get_account());
        }

        $this->load->view('v2/donations/single', array(
            'donation' => array_shift(prep_donations(array($donation)))
        ));

    }

    /**
     * Handles display and edit of single donation
     *
     * Handles:
     *
     *  * GET /api/v2/donations/{token}
     *  * POST /api/v2/donations/{token}
     *
     * Accepts via GET:
     *
     *  * related {String}
     *
     * Accepts via POST:
     *
     *  * first_name {String}
     *  * last_name {String}
     *  * billing_address1 {String}
     *  * billing_city {String}
     *  * billing_state {String}
     *  * billing_postal_code {String}
     *  * billing_country {String}
     *  * donation_total {Float}
     *  * donation_level {String}
     *  * donation_level_id {Int}
     *  * donation_date {String}
     *  * email_address {String}
     *  * contact {Boolean}
     *
     * @return Object Donation_model
     */
    public function token() {
        $donation = $this->Donation_model
            ->select_complete()
            ->where('donations.account_id', $this->authorization->account)
            ->where('donations.donation_token', $this->uri->rsegment(3))
            ->limit(1)
            ->find();

        $data = $this->input->post_body;

        if( !$data && $_SERVER['REQUEST_METHOD'] == 'POST' ) {
            $this->output->show_error('Your request body is missing or malformed', 400, 'bad_request');
            return;
        }

        if( $data ) {

            $campaign = $donation[0]->campaign;
            $opportunity = $donation[0]->opportunity;

            if( !$campaign ) {
                $campaign = $opportunity->campaign;
            }

            $old_amount = $donation[0]->amount;

            $donation = $this->_process_post($donation[0]);
            if( !$donation ) {
                return;
            }

            $donation = $this->_process_custom_responses($donation, $campaign);
            if( !$donation ) {
                return;
            }

            // no email address
            if( !$donation->email_address ) {
                $this->output->show_error('Email address is a required field', 400, 'invalid_email');
            }

            $matched_donation_level = false;
            $current_donation_amount = false;
            if( $donation->donation_level_id ) {
                $matched_donation_level =  $this->_find_donation_level($donation->donation_level_id,$campaign->donation_levels);
            }
            if( $donation->amount ) {
                $current_donation_amount = $donation->amount;
            }

            // sent a level that doesn't go with this campaign
            if( $donation->donation_level_id && !$matched_donation_level ) {
                $this->output->show_error('The donation level provided does not belong to this campaign', 400, 'invalid_donation');
                return;
            }

            // sent a level but not an amount
            if( $matched_donation_level && !$current_donation_amount ) {

                $donation->amount = $matched_donation_level->amount;
                $donation->description = $matched_donation_level->description;

            // sent level and an amount, assume the amount is the charge total
            } elseif( $matched_donation_level && $current_donation_amount ) {

                $donation->amount = $current_donation_amount;
                $donation->donation_level_id = $donation->donation_level_id;
                $donation->description = $matched_donation_level->description;

            // did not send a level, but did send an amount
            } elseif( !$matched_donation_level && $current_donation_amount ) {

                $donation->donation_level_id = false;
                $donation->amount = $current_donation_amount;

            // sent neither
            } elseif( !$matched_donation_level && !$current_donation_amount ) {

                $this->output->show_error('You must provide a donation level and/or donation amount', 400, 'invalid_donation');
                return;

            }

            if( $donation->amount != floor($donation->amount) || !floor($donation->amount) ) {
                $this->log->entry('Invalid donation amount');
                $this->output->show_error('Donations must be in whole dollar amounts', 400, 'invalid_donation');
                return;
            }

            if( ($campaign->minimum_donation_amount && $donation->amount < $campaign->minimum_donation_amount)
                || (!$campaign->minimum_donation_amount && $donation->amount < 5) ) {

                $min = $campaign->minimum_donation_amount ? $campaign->minimum_donation_amount : 5;
                $this->log->entry('Invalid donation amount');
                $this->output->show_error('Donations must greater than or equal to '.$min, 400, 'invalid_donation');
                return;
            }

            if( (strlen($donation->description) && !$donation->donation_level_id)
                || (!strlen($donation->description) && $donation->donation_level_id) ) {

                $this->output->show_error('Both donation level and donation level id are required', 400, 'bad_request');
                return;
            }

            // update supporter model
            $supporter = $this->Supporter_model
                ->where('account_id', $this->authorization->account)
                ->where('email_address', $donation->email_address)
                ->find();

            if( !$supporter || !count($supporter) ) {
                $supporter = new Supporter_model;
                $supporter->account_id          = $this->authorization->account;
                $supporter->email_address       = $donation->email_address;
                $supporter->first_name          = $donation->first_name;
                $supporter->last_name           = $donation->last_name;
                $supporter->street_address      = $donation->billing_address1;
                $supporter->city                = $donation->billing_city;
                $supporter->state               = $donation->billing_state;
                $supporter->postal_code         = $donation->billing_postal_code;
                $supporter->country             = $donation->billing_country;

                $supporter->save_entry();
            } else {
                $supporter = $supporter[0];
            }

            $old_supporter = false;
            if( $donation->supporter_id && $donation->supporter_id != $supporter->id ) {
                // supporter changed
                $old_supporter = $this->Supporter_model
                    ->where('id', $donation->supporter_id)
                    ->find();
                if( $old_supporter && count($old_supporter) ) {
                    $old_supporter = $old_supporter[0];
                }
            }
            $donation->supporter_id = $supporter->id;

            $donation->supporter_id = $supporter->id;

            $donation->save_entry();
            $supporter->save_entry();

            if( $old_supporter ) {
                $old_supporter->save_entry();
            }

            // just make sure we have all the right data
            $donation = $this->Donation_model
                ->select_complete()
                ->get_entry($donation->id);

            // check for hooks and generate a task call
            $hooks = $this->Hook_model
                ->where('account_id', $this->authorization->account)
                ->where('status', true)
                ->where('event', 'donation.edit')
                ->find();
            foreach( $hooks as $hook ) {
                $task = $this->Task_model
                    ->createTask($hook, $donation);
                $task->save_entry();
            }

            // make sure to update parent(s)
            if( $opportunity ) {
                $opportunity->current = $opportunity->current - $old_amount + $donation->amount;
                $opportunity->save_entry();
            }
            $campaign->current = $campaign->current - $old_amount + $donation->amount;
            $campaign->save_entry();
        }

        $this->load->view('v2/donations/single', array(
            'donation' => array_shift(prep_donations($donation))
        ));
    }

    private function _process_custom_responses($donation, $campaign) {
        $data = json_decode(json_encode($this->input->post_body), true);

        if( !array_key_exists('custom_responses', $data) ) {
            return $donation;
        }

        $responses = $data['custom_responses'];

        if( !is_array($campaign->custom_fields) ) {
            return $donation;
        }

        $err = array();
        foreach( $campaign->custom_fields as $f ) {
            if( $f->field_required && (!array_key_exists($f->id, $responses) || !$responses[$f->id]) ) {
                $err[] = $f->field_label.' is required';
            }
        }

        if( count($err) && !$donation->offline ) {
            $this->output->show_error('Required fields: '.implode(', ', $err), 400, 'missing_parameters');
            return false;
        }

        $donation->set_responses($responses);

        return $donation;
    }

    private function _process_post($donation) {
        $data = json_decode(json_encode($this->input->post_body), true);

        // sanity
        if( !$donation->id ) {
            foreach( $this->required as $r ) {
                if( !array_key_exists($r, $data) ) {
                    $this->output->show_error('Required fields: '.implode(', ', $this->required), 400, 'missing_parameters');
                    return false;
                }
            }
        }

        foreach( $data as $k => $v ) {
            if( !in_array($k, $this->allowed) ) {
                continue;
            }

            if( $k == 'card' ) {
                continue;
            }

            if( $k == 'campaign' || $k == 'opportunity' ) {
                continue;
            }

            if( $k == 'donation_level' ) {
                $k = 'description';
            } elseif( $k == 'donation_total' ) {
                $k = 'amount';
            } elseif( $k == 'custom_responses' ) {
                continue;
            } elseif( $k == 'offline' ) {
                continue;
            }

            $donation->$k = $this->security->xss_clean($v);
        }

        if( !property_exists($donation, 'donation_date') || !$donation->donation_date ) {
            $donation->donation_date = date('Y-m-d H:i:s');
        }

        if( !$donation->id ) {
            $donation->offline = true;
        }

        $donation->complete = 1;

        return $donation;
    }
    private function _find_donation_level($donation_level_id,$campaign_donation_levels){

        foreach ( $campaign_donation_levels as $level ) {
            if ( $donation_level_id == $level->id ) {
                return $level;
            }
        }

        return false;
    }
}