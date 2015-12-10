<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'/libraries/stripe/Stripe.php');
/**
 * Donate controller
 *
 * @class Donate
 * @extends CI_Controller
 */
class Donate extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->library('giving_impact');
        $this->load->library('form_validation');

        $this->load->helper('number');
        $this->load->helper('typography');
        $this->load->helper('api');
        $this->load->helper('notification');

        $this->load->model('Account_model');
        $this->load->model('Campaign_model');
        $this->load->model('Opportunity_model');
        $this->load->model('Supporter_model');
        $this->load->model('Plan_model');
        $this->load->model('Link_model');
        $this->load->model('Task_model');
        $this->load->model('Hook_model');
        $this->load->model('Transaction_model');

        $referrer = $this->input->get('referrer');
        if( $this->session->flashdata('referrer') ) {
            $this->session->keep_flashdata('referrer');
        }
        if( $referrer && strlen($referrer) ) {
            $this->session->set_flashdata('referrer', $referrer);
        }
    }

    /**
     * Fetches campaign and/or opportunity based on URL segments
     *
     * @return Array ['campaign' => Campaign_model, 'opportunity' => Opportunity_model or FALSE]
     */
    private function fetchCampaigns() {
        $hash = $this->uri->segment(2);

        if( !$hash ) {
            show_error('Unknown campaign id');
            return;
        }

        $campaign = $this->Campaign_model
            ->where('campaigns.campaign_token', $hash)
            ->find();

        $opportunity = false;

        if( !count($campaign) || $campaign[0]->campaign_id ) {
            $opportunity = $this->Opportunity_model
                ->where('campaigns.campaign_token', $hash)
                ->find();
        }

        if( $opportunity && count($opportunity) ) {
            $opportunity = $opportunity[0];
            $campaign = $opportunity->campaign;
        } else {
            $campaign = $campaign[0];
            $opportunity = false;
        }

        return array(
            'campaign'    => $campaign,
            'opportunity' => $opportunity
        );
    }

    /**
     * Index action
     *
     * Loads 'donate/index' view with
     *
     *  * campaign Campaign_model
     *  * is_opportunity Boolean
     *  * account Account_model
     *  * hash String
     *
     */
	public function index() {
	    $r = $this->fetchCampaigns();

	    $opportunity = $r['opportunity'];
	    $campaign = $r['campaign'];

        if( !$campaign->id && !$opportunity->id ) {
            show_error('No campaign found');
            return;
        }

        $account = $this->Account_model
            ->where('id', $campaign->account_id)
            ->find();

        $this->load->library('typography');

        if( !$account[0]->currency ) {
            $this->load->view('donate/error', array(
                'campaign'      => $opportunity ? $opportunity : $campaign,
                'is_opportunity'=> $campaign ? false : true,
                'account'       => $account[0],
                'hash'          => $this->uri->segment(2)
            ));
            return;
        }

        $this->load->view('donate/index', array(
            'campaign'      => $opportunity ? $opportunity : $campaign,
            'is_opportunity'=> $campaign ? false : true,
            'account'       => $account[0],
            'hash'          => $this->uri->segment(2)
        ));
	}

    /**
     * Checkout handler presents the user with the checkout form
     *
     * Loads 'donation/checkout' view with
     *
     *  * campaign Campaign_model
     *  * is_opportunity Boolean
     *  * account Account_model
     *  * hash String
     *  * donation_level Object
     *  * additional_donation Float
     *  * donation_amount Float
     *  * donation_total Float
     *  * responses Array
     *
     */
	public function checkout($charge_error = false) {
	    $this->session->keep_flashdata('fields');

	    $r = $this->fetchCampaigns();

	    $opportunity = $r['opportunity'];
	    $campaign = $r['campaign'];

        $translate = array(
            '$' => '',
            ',' => ''
        );

        if( $campaign->enable_donation_levels ) {
            if( $this->input->post('donation_level') != 'open' ) {
                $level = $this->db
                    ->where('id', $this->input->post('donation_level'))
                    ->get('campaign_levels')
                    ->result();
            }
            $addtl = str_replace(array_keys($translate), $translate, $this->input->post('additional_donation'));
            if( !$charge_error ) {
                $addtl = $addtl * 100;
            }
        } else {
            $amt = str_replace(array_keys($translate), $translate, $this->input->post('donation_amount'));
            if( !$charge_error ) {
                $amt = $amt * 100;
            }

        }

        $responses = array();
        if( $campaign->custom_fields && !$charge_error ) {
            $responses = $this->input->post('fields');
            $this->session->set_flashdata('fields', serialize($responses));

            $errors = array();
            foreach( $campaign->custom_fields as $f ) {
                if( $f['required'] && $f['status'] && !$responses[$f['field_id']] ) {
                    $errors[] = $f['field_label'].' is required';
                }
            }

            if( count($errors) ) {
                $this->session->set_flashdata('customerror', implode('<br />', $errors));
                if( isset($addtl) ) {
                    $this->session->set_flashdata('addtl', $addtl);
                    if( isset($level) ) {
                        $this->session->set_flashdata('level', $level[0]->id);
                    }
                } else {
                    $this->session->set_flashdata('amt', $amt);
                }

                redirect('donate/'.$this->uri->segment(2));
                return;

            }
        }

        $account = $this->Account_model
            ->where('id', $campaign->account_id)
            ->find();

        if( !$account[0]->currency ) {
            $this->session->set_flashdata('error', 'This campaign is not currently accepting donations');
            redirect('donate/'.$this->uri->segment(2));
            return;
        }

        $total = 0;
        if( isset($level) && $level != 'open' ) {
            $total += $level[0]->amount;
        }
        if( isset($addtl) ) {
            $total += str_replace('$', '', $addtl);
        }
        if( isset($amt) ) {
            $total += str_replace('$', '', $amt);
        }

        if( $total != floor($total) ) {
            $this->session->set_flashdata('error', 'Your donation must be in dollar increments');
            if( isset($addtl) ) {
                $this->session->set_flashdata('addtl', $addtl);
                if( isset($level) ) {
                    $this->session->set_flashdata('level', $level[0]->id);
                }
            } else {
                $this->session->set_flashdata('amt', $amt);
            }

            redirect('donate/'.$this->uri->segment(2));
            return;
        }

        if( !$total ) {
            $this->session->set_flashdata('error', 'You must select a donation amount');
            redirect('donate/'.$this->uri->segment(2));
            return;
        }

        $total = floor($total);

        $min = $opportunity ? $opportunity->minimum_donation_amount : $campaign->minimum_donation_amount;
        if( $total < $min ) {
            $this->session->set_flashdata('level', $this->input->post('donation_level'));
            $this->session->set_flashdata('addtl', $this->input->post('additional_donation'));
            $this->session->set_flashdata('error', 'Minimum donation is '.money_format('%n', $min/100));
            redirect('donate/'.$this->uri->segment(2));
            return;
        }

        $this->load->view('donate/checkout', array(
            'campaign'      => $opportunity ? $opportunity : $campaign,
            'is_opportunity'=> $campaign ? false : true,
            'account'       => $account[0],
            'hash'          => $this->uri->segment(2),
            'donation_level'=> isset($level) ? $level[0] : false,
            'additional_donation' => isset($addtl) ? $addtl : false,
            'donation_amount' => isset($amt) ? $amt : false,
            'donation_total'=> $total,
            'responses'     => $responses,
            'charge_error'  => $charge_error
        ));
	}

    /**
     * Donation process handler. This is a URL callback that processes
     * the donation, sends the request to the Stripe API and records
     * the result. A successful donation will send the user to
     * donate/XXXXX/complete, a failure will send the user back to the
     * donation form.
     *
     */
	public function process() {
	    $r = $this->fetchCampaigns();

	    $opportunity = $r['opportunity'];
	    $campaign = $r['campaign'];

	    if( $campaign->enable_donation_levels ) {
	        if( $this->input->post('donation_level') ) {
    	        $level = $this->db
        	        ->where('id', $this->input->post('donation_level'))
        	        ->get('campaign_levels')
        	        ->result();
	        }
	        $addtl = $this->input->post('additional_donation');
	    } else {
	        $amt = $this->input->post('donation_amount');
	    }

	    $responses = array();
	    if( $campaign->custom_fields ) {
	        $responses = $this->input->post('fields');
	    }

	    $account = $this->Account_model
    	    ->where('id', $campaign->account_id)
    	    ->find();

        if( !$account[0]->currency ) {
            $this->session->set_flashdata('error', 'This campaign is not currently accepting donations');
            redirect('donate/'.$this->uri->segment(2));
            return;
        }

	    $total = 0;
	    if( isset($level) ) {
	        $total += $level[0]->amount;
	    }
	    if( isset($addtl) ) {
	        $total += $addtl;
	    }
	    if( isset($amt) ) {
	        $total += $amt;
	    }

	    $card        = $this->input->post('stripe_token');
	    $first_name  = $this->input->post('first_name');
	    $last_name   = $this->input->post('last_name');
	    $street      = $this->input->post('street');
	    $city        = $this->input->post('city');
	    $state       = $this->input->post('state');
	    $postal      = $this->input->post('postal');
	    $country     = $this->input->post('country');
	    $email       = $this->input->post('email');

	    $data = new stdClass;
	    $data->created_at        = date('Y-m-d H:i:s');
	    $data->updated_at        = $data->created_at;
	    $data->donation_date     = $data->created_at;
	    $data->first_name        = $first_name;
	    $data->last_name         = $last_name;
	    $data->billing_address1  = $street;
	    $data->billing_city      = $city;
	    $data->billing_state     = $state;
	    $data->billing_postal_code = $postal;
	    $data->billing_country   = $country;
	    $data->email_address     = $email;
	    $data->amount            = $total;
	    $data->contact           = 1;
	    $data->account_id        = $account[0]->id;
	    $data->donation_token    = api_generate_token();

	    if( $opportunity ) {
	        $data->campaign_id = $opportunity->id;
	        $data->campaign_group_id = $campaign->id;
	    } else {
	        $data->campaign_id = $data->campaign_group_id = $campaign->id;
	    }

	    if( isset($level) ) {
	        $data->description = $level[0]->description;
            $data->donation_level_id = $level[0]->id;
	    }

        // update supporter model
        $supporter = $this->Supporter_model
            ->where('account_id', $account[0]->id)
            ->where('email_address', $data->email_address)
            ->find();

        if( !$supporter || !count($supporter) ) {
            $supporter = new Supporter_model;
            $supporter->account_id          = $account[0]->id;
            $supporter->email_address       = $data->email_address;
            $supporter->first_name          = $data->first_name;
            $supporter->last_name           = $data->last_name;
            $supporter->street_address      = $data->billing_address1;
            $supporter->city                = $data->billing_city;
            $supporter->state               = $data->billing_state;
            $supporter->postal_code         = $data->billing_postal_code;
            $supporter->country             = $data->billing_country;

            $supporter->save_entry();
        } else {
            $supporter = $supporter[0];
        }

        $data->supporter_id = $supporter->id;


	    $this->db->insert('donations', $data);
	    $donation_id = $this->db->insert_id();
        $supporter->save_entry();

        $data->id = $donation_id;
        // check for hooks and generate a task call
        $hooks = $this->Hook_model
            ->where('account_id', $account[0]->id)
            ->where('status', true)
            ->where('event', 'donation.create')
            ->find();
        foreach( $hooks as $hook ) {
            $task = $this->Task_model
                ->createTask($hook, $data);
            $task->save_entry();
        }

        $planName = $account[0]->account_name;
        $planName .= ' '.($opportunity ? $opportunity->title : $campaign->title);
        $planName .= ' '.$data->donation_token;

        if( ($this->input->post('allow_recurring') && $campaign->frequency_type == 2) || $campaign->frequency_type == 1 ) {
            $interval = false;
            $period = false;
            switch($campaign->frequency_period) {
                case 12:
                    $interval = 'year';
                    $period = 1;
                    break;
                case 6:
                    $interval = 'month';
                    $period = 6;
                    break;
                case 3:
                    $interval = 'month';
                    $period = 3;
                case 1:
                default:
                    $interval = 'month';
                    $period = 1;
                    break;
            }

            try {
                $plan = new Plan_model;
                $plan->account_id   = $account[0]->id;
                $plan->currency     = $account[0]->currency;
                $plan->donation_total = $total;
                $plan->stripe_plan_id = $planName;
                $plan->save_entry();

                $this->db
                    ->where('id', $donation_id)
                    ->update('donations', array('plan_id' => $plan->id));

                $stripe_plan = Stripe_Plan::create(
                    array(
                        "amount"    => $total,
                        "currency"  => $account[0]->currency,
                        "name"      => $planName,
                        "id"        => 'tt'.$plan->id,
                        "interval"  => $interval,
                        "interval_count" => $period,
                        "metadata"  => array(
                            "donation" => $donation_id,
                            "campaign" => $campaign->id,
                            "opportunity"=> $opportunity ? $opportunity->id : ''
                        )
                    ),
                    $this->config->item('stripe_secret_key')
                );

                $stripe_cust = Stripe_Customer::create(
                    array(
                        "description"       => $supporter->first_name.' '.$supporter->last_name.' ('.$supporter->email_address.')',
                        "plan"              => $stripe_plan->id,
                        "source"            => $card
                    ),
                    $this->config->item('stripe_secret_key')
                );
            } catch( Exception $e ) {
                return $this->checkout($e->getMessage());
            }

            if( $stripe_cust->id ) {
                $this->db
                    ->where('id', $donation_id)
                    ->update('donations', array('complete' => 1, 'stripe_charge_id' => $stripe_cust->id));
            }

        } else {
            try {
                $charge = Stripe_Charge::create(
                    array(
                        'amount'    => $total,
                        'currency'  => $account[0]->currency,
                        'card'      => $card,
                        'description' => $email
                    ),
                    $this->config->item('stripe_secret_key')
                );
            } catch( Exception $e ) {
                return $this->checkout($e->getMessage());
            }

            if( $charge->id ) {
                $this->db
                    ->where('id', $donation_id)
                    ->update('donations', array('complete' => 1, 'stripe_charge_id' => $charge->id));
            }
        }

        $campaign->current = $campaign->current + $total;
        $campaign->total_donations = $campaign->total_donations + 1;
        $campaign->save_entry();
        if( $opportunity ) {
            $opportunity->current = $opportunity->current + $total;
            $opportunity->total_donations = $opportunity->total_donations + 1;
            $opportunity->save_entry();
        }

	    if( $this->session->flashdata('fields') ) {
	        $responses = unserialize($this->session->flashdata('fields'));
	        if( is_array($responses) && count($responses) ) {
	            foreach( $responses as $id => $resp ) {
	                $field = $this->db
	                    ->where('id', $id)
	                    ->get('custom_texts')
	                    ->result();

	                $data = new stdClass;
	                $data->user_response    = $resp;
	                $data->field_id         = $id;
	                $data->donation_id      = $donation_id;
	                $data->created_at       = date('Y-m-d H:m:s');
	                $data->updated_at       = $data->created_at;
	                $data->field_label      = $field[0]->field_label;

	                $this->db
	                    ->insert('custom_form_responses', $data);
	            }
	        }
	    }

        $txn = new Transaction_model;
        $txn->donation_id         = $donation_id;
        $txn->stripe_id           = $charge->id;
        $txn->type                = 'charge';
        $txn->total               = $total;
        $txn->save_entry();


	    $this->session->set_flashdata('donation', $donation_id);
        $this->session->set_flashdata('send_receipt', true);

        $vars = 'utm_source=';
        $vars .= $this->input->get('utm_source') ? $this->input->get('utm_source') : 'direct';
        $vars .= '&utm_medium=';
        $vars .= $this->input->get('utm_medium') ? $this->input->get('utm_medium') : 'link';
        $vars .= '&utm_campaign=';
        $vars .= $campaign->title;

	    redirect(site_url('donate/'.$this->uri->segment(2).'/complete?'.$vars));
	    return;
   	}

    /**
     * Complete donation.
     *
     * Loads 'donation/complete' view with
     *
     *  * campaign Campaign_model
     *  * is_opportunity Boolean
     *  * acount Account_model
     *  * donation Donation_model
     *  * share_url String
     *
     */
   	public function complete() {
	    $r = $this->fetchCampaigns();

	    $opportunity = $r['opportunity'];
	    $campaign = $r['campaign'];


	    $account = $this->Account_model
    	    ->where('id', $campaign->account_id)
    	    ->find();

	    $this->session->keep_flashdata('donation');
	    $donation_id = $this->session->flashdata('donation');

	    $donation = $this->db
	        ->where('id', $donation_id)
	        ->get('donations')
	        ->result();

        if( $campaign->send_receipt && $this->session->flashdata('send_receipt') ) {
            $c = $opportunity ? $opportunity : $campaign;
            notify_receipt($donation[0], $c, $account[0]);
        }

        $this->load->view('donate/complete', array(
            'campaign'      => $opportunity ? $opportunity : $campaign,
            'is_opportunity'=> $campaign ? false : true,
            'account'       => $account[0],
            'donation'      => $donation[0],
        ));
   	}

    /**
     * Contact is an Ajax callback only. It exists to record if a user wishes
     * to be contacted by the org or not. Accepts the donation HASH as the 2nd
     * URL segment and 'contact' as a BOOLEAN as a POST parameter.
     *
     * @example
     *  POST /donate/XXXXXX/contact
     *  contact=1
     */
   	public function contact() {
	    $this->session->keep_flashdata('donation');

	    $hash = $this->uri->segment(2);

   	    $donation = $this->db
   	        ->where('donation_token', $hash)
   	        ->get('donations')
   	        ->result();

   	    if( count($donation) ) {
   	        $data = array();
   	        $data['contact'] = $this->input->post('contact') ? 1 : 0;

   	        $this->db
   	            ->where('donation_token', $hash)
   	            ->update('donations', $data);
   	        return;
   	    }

   	    return false;
   	}

}
