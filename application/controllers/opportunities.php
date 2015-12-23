<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'/libraries/stripe/Stripe.php');

/**
 * Opportunities controller
 *
 * @class Opportunities
 * @extends CI_Controller
 */
class Opportunities extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->library('auth');
        $this->load->library('giving_impact');
        $this->load->library('form_validation');

        $this->load->helper('number');
        $this->load->helper('file');
        $this->load->helper('export');
        $this->load->helper('typography');
        $this->load->helper('notification');

        $this->load->model('Transaction_model');
        $this->load->model('Campaign_model');
        $this->load->model('Opportunity_model');
        $this->load->model('Donation_model');
        $this->load->model('Account_model');
        $this->load->model('Supporter_model');

        if( !$this->auth->is_authorized() ) {
            $this->auth->handle_expired();
            return;
        }
    }

    /**
     * Index action handler
     *
     * Loads 'opportunities/index' view with
     *  * campaign Campaign_model
     *  * opportunities Array of Opportunity_models
     *  * previous String
     *  * next String
     *
     */
    public function index() {
        if( !$this->uri->segment(2) ) {
            show_error('Campaign token is required');
            return;
        }

        $offset = $this->uri->segment(4) ? $this->uri->segment(4) : 0;
        $max = 20;

        $campaign = $this->giving_impact
            ->campaign
            ->fetch($this->uri->segment(2));

        $opportunities = $campaign
            ->opportunities
            ->sort('title');
        if( $offset ) {
            $opportunities->offset($offset);
        }
        $opportunities = $opportunities
            ->status('active')
            ->limit(($max+1))
            ->fetch();

        $prev = $next = 'campaigns/'.$campaign->id_token.'/opportunities';

        if( count($opportunities) > $max ) {
            array_pop($opportunities);
            $next .= '/'.($max+$offset);
        } else {
            $next = false;
        }

        if( !$offset ) {
            $prev = false;
        } else {
            $prev .= '/'.($offset-$max);
        }


        $this->load->view('opportunities/index', array(
            'campaign' => $campaign,
            'opportunities' => $opportunities,
            'previous' => $prev,
            'next' => $next
        ));
    }

    /**
     * View only inactive opportunities
     *
     * Loads 'opportunities/inactive' view with:
     *
     *  * inactive Array of Opportunity_models
     *  * campaigns Array of Opportunity_models
     * @return [type] [description]
     */
    public function inactive() {
        if( !$this->uri->segment(2) ) {
            show_error('Campaign token is required');
            return;
        }

        $campaign = $this->giving_impact
            ->campaign
            ->fetch($this->uri->segment(2));

        $opportunities = $campaign
            ->opportunities
            ->sort('title');

        $opportunities = $opportunities
            ->status('inactive')
            ->limit(1000)
            ->fetch();

        $this->load->view('opportunities/inactive', array(
            'opportunities' => $opportunities,
            'campaign' => $campaign
        ));
    }

    /**
     * View single opportunity
     *
     * Loads 'opportunities/view' view with
     *
     *  * campaign Campaign_model
     *  * opportunity Opportunity_model
     *  * donations Array of Donation_models
     *  * stats Array of Stats_models
     *
     */
    public function view() {

        if( !$this->uri->segment(2) ) {
            show_error('Campaign token is required');
            return;
        }

        $campaign = $this->giving_impact
            ->campaign
            ->fetch($this->uri->segment(2));

        if( !$this->uri->segment(4) ) {
            show_error('Campaign token is required');
            return;
        }

        $opportunity = $this->giving_impact
            ->opportunity
            ->fetch($this->uri->segment(4));

        $donations = $opportunity
            ->donations
            ->sort('donation_date|desc')
            ->limit(10)
            ->fetch();

        $stats = $opportunity
            ->stats
            ->limit(10)
            ->fetch();

        $this->load->view('opportunities/view.php', array(
            'campaign' => $campaign,
            'opportunity' => $opportunity,
            'donations' => $donations,
            'stats' => $stats
        ));


    }

    /**
     * Edit or create new opportunity
     *
     * Loads 'opportunities/edit' view with
     *
     *  * campaign Campaign_model
     *  * opportunity Opportunity_model
     *
     * 'opportunity' will be an empty model when creating a new opportunity
     *
     */
    public function edit() {
        if( !$this->uri->segment(2) ) {
            show_error('Campaign token is required');
            return;
        }

        $campaign = $this->giving_impact
            ->campaign
            ->fetch($this->uri->segment(2));

        if( !$this->uri->segment(4) ) {
            show_error('Campaign token is required');
            return;
        }

        $opportunity = $this->giving_impact
            ->opportunity
            ->fetch($this->uri->segment(4));

        if( $_POST && count($_POST) ) {
            if( !$this->form_validation->run('opportunity') ) {
                $this->load->view('opportunities/edit', array(
                    'campaign' => $campaign,
                    'opportunity' => $opportunity
                ));
                return;
            }

            $opportunity->campaign_token    = $campaign->id_token;
            $opportunity->title             = $this->input->post('title');
            $opportunity->description       = $this->input->post('description');
            $opportunity->status            = $this->input->post('status') ? true : false;
            $opportunity->donation_target   = $this->input->post('target') * 100;
            $opportunity->youtube_id        = $this->input->post('youtube');

            if( $_FILES && count($_FILES) ) {
                $file = $_FILES['file'];
                $type = $file['type'];
                $path = $file['tmp_name'];

                if( $type && $path ) {
                    $opportunity->image_type = $type;
                    $opportunity->image_file = base64_encode(file_get_contents($path));
                }
            }

            if( $this->input->post('remove-image') ) {
                $opportunity->image_type = 'nil';
                $opportunity->image_file = false;
            }

            if( $campaign->campaign_fields ) {
                $responses = $this->input->post('fields');

                $errors = array();

                $opportunity->campaign_responses = array();
                foreach( $campaign->campaign_fields as $f ) {
                    if( $f->required && $f->status && !$responses[$f->field_id] ) {
                        $errors[] = $f->field_label.' is required';
                        break;
                    }

                    if( !array_key_exists($f->field_id, $responses) ) {
                        continue;
                    }
                    $data = new stdClass;
                    $data->response             = $responses[$f->field_id];
                    $data->campaign_field_id    = $f->field_id;

                    $opportunity->campaign_responses[] = $data;

                }

                if( count($errors) ) {
                    $this->session->set_flashdata(array(
                            'message' => 'Some required fields are missing. Please check the form and try again.',
                            'message_type' => 'alert'
                    ));
                    redirect(site_url('campaigns/'.$campaign->id_token.'/opportunities/'.$opportunity->id_token.'/edit'));
                    return;
                }

            }

            if( $this->input->post('supporter_email_address') ) {
                $supporters = array();
                foreach($this->input->post('supporter_email_address') as $addr) {
                    $supporters[] = array('email_address' => $addr);
                }

                $opportunity->supporters = $supporters;
            }

            try {
                $res = $opportunity
                  ->save();
            } catch( \MODL\GivingImpact\Exception $e ) {
                $this->session->set_flashdata(array(
                        'message' => $e->getMessage(),
                        'message_type' => 'alert'
                ));
                redirect(site_url('campaigns/'.$campaign->id_token.'/opportunities/'.$opportunity->id_token.'/edit'), 'header');
                return;
            }

            $this->session->set_flashdata('message', 'Giving Opportunity Saved.');
            redirect(site_url('campaigns/'.$campaign->id_token.'/opportunities/'.$opportunity->id_token), 'header');
            return;
        }

        $this->load->view('opportunities/edit', array(
            'campaign' => $campaign,
            'opportunity' => $opportunity
        ));

    }

    /**
     * Promote view action
     *
     * Loads 'opportunities/promote' view with
     *
     *  * campaign Campaign_model
     *  * opportunity Opportunity_model
     *
     */
    public function promote() {
        if( !$this->uri->segment(2) ) {
            show_error('Campaign token is required');
            return;
        }

        $campaign = $this->giving_impact
            ->campaign
            ->fetch($this->uri->segment(2));

        if( !$this->uri->segment(4) ) {
            show_error('Campaign token is required');
            return;
        }

        $opportunity = $this->giving_impact
            ->opportunity
            ->fetch($this->uri->segment(4));

        $this->load->view('opportunities/promote', array(
            'campaign' => $campaign,
            'opportunity' => $opportunity
        ));

    }

    /**
     * New opportunity handler. Uses the existing edit view
     * but handles logic specific to creating new opportunities
     *
     */
    public function new_opportunity() {
        if( !$this->uri->segment(2) ) {
            show_error('Campaign token is required');
            return;
        }

        $campaign = $this->giving_impact
            ->campaign
            ->fetch($this->uri->segment(2));

        if( $_POST && count($_POST) ) {
            if( !$this->form_validation->run('opportunity') ) {
                $this->load->view('opportunities/edit', array(
                    'campaign' => $campaign,
                    'opportunity' => $this->giving_impact->opportunity
                ));
                return;
            }

            $data = array(
                'campaign_token'    => $campaign->id_token,
                'title'             => $this->input->post('title'),
                'description'       => $this->input->post('description'),
                'status'            => $this->input->post('status') ? true : false,
                'donation_target'   => $this->input->post('target') * 100,
                'youtube_id'        => $this->input->post('youtube'),
                'analytics_id'      => $this->input->post('analytics_id')
            );

            if( $_FILES && count($_FILES) ) {
                $file = $_FILES['file'];
                $type = $file['type'];
                $path = $file['tmp_name'];

                if( $type && $path ) {
                    $data['image_type'] = $type;
                    $data['image_file'] = base64_encode(file_get_contents($path));
                }
            }

            if( $campaign->campaign_fields ) {
                $responses = $this->input->post('fields');

                $errors = array();

                $data['campaign_responses'] = array();
                foreach( $campaign->campaign_fields as $f ) {
                    if( $f->required && $f->status && !$responses[$f->field_id] ) {
                        $errors[] = $f->field_label.' is required';
                        break;
                    }

                    if( !array_key_exists($f->field_id, $responses) ) {
                        continue;
                    }

                    $out = array();
                    $out['response']                = $responses[$f->field_id];
                    $out['campaign_field_id']       = $f->field_id;

                    $data['campaign_responses'][] = $out;

                }

                if( count($errors) ) {
                    $this->session->set_flashdata(array(
                            'message' => 'Some required fields are missing. Please check the form and try again.',
                            'message_type' => 'alert'
                    ));
                    redirect(site_url('campaigns/'.$campaign->id_token.'/opportunities/new'));
                    return;
                }

            }

            $res = $this->giving_impact
                ->opportunity
                ->create($data);

            $this->session->set_flashdata('message', 'Giving Opportunity Created.');
            redirect(site_url('campaigns/'.$campaign->id_token.'/opportunities'), 'header');
            return;
        }

        $this->load->view('opportunities/edit', array(
            'campaign' => $campaign,
            'opportunity' => $this->giving_impact->opportunity
        ));

    }

    /**
     * Donation view method
     *
     * Loads 'opporunities/donations' view with
     *
     * * campaign Campaign_model
     * * opportunity Opportunity_model
     * * donations Array of Donation_models
     * * previous String
     * * next String
     *
     */
    public function donations() {
        if( !$this->uri->segment(2) ) {
            show_error('Campaign token is required');
            return;
        }

        $campaign = $this->giving_impact
            ->campaign
            ->fetch($this->uri->segment(2));

        if( !$this->uri->segment(4) ) {
            show_error('Campaign token is required');
            return;
        }

        $opportunity = $this->giving_impact
            ->opportunity
            ->fetch($this->uri->segment(4));

        $offset = $this->uri->segment(6) ? $this->uri->segment(6) : 0;
        $max = 20;

        $donations = $opportunity
            ->donations
            ->sort('donation_date|desc');
        if( $offset ) {
            $donations->offset($offset);
        }
        $donations = $donations
            ->limit(($max+1))
            ->fetch();

        $prev = $next = 'campaigns/'.$campaign->id_token.'/opportunities/'.$opportunity->id_token.'/donations';

        if( count($donations) > $max ) {
            array_pop($donations);
            $next .= '/'.($max+$offset);
        } else {
            $next = false;
        }

        if( !$offset ) {
            $prev = false;
        } else {
            $prev .= '/'.($offset-$max);
        }


        $this->load->view('opportunities/donations.php', array(
            'campaign' => $campaign,
            'opportunity' => $opportunity,
            'donations' => $donations,
            'previous' => $prev,
            'next' => $next
        ));
    }

    /**
     * View donation handler
     *
     * Loads 'opportunities/donation' view
     *
     *  * campaign Campaign_model
     *  * opportunity Opportunity_model
     *  * donation Donation_model
     *
     */
    public function donation() {
        if( !$this->uri->segment(2) ) {
            show_error('Campaign token is required');
            return;
        }

        $campaign = $this->giving_impact
            ->campaign
            ->fetch($this->uri->segment(2));

        if( !$this->uri->segment(4) ) {
            show_error('Campaign token is required');
            return;
        }

        $donation = $this->giving_impact
            ->donation
            ->fetch($this->uri->segment(6));

        $opportunity = $this->giving_impact
            ->opportunity
            ->fetch($this->uri->segment(4));

        $this->load->view('opportunities/donation.php', array(
            'campaign' => $campaign,
            'opportunity' => $opportunity,
            'donation' => $donation
        ));

    }

    /**
     * Edit donation handler
     *
     * Loads 'opportunities/donation' edit view
     *
     *  * campaign Campaign_model
     *  * opportunity Opportunity_model
     *  * donation Donation_model
     *
     */
    public function donation_edit() {
        if( !$this->uri->segment(2) ) {
            show_error('Campaign token is required');
            return;
        }

        $campaign = $this->giving_impact
            ->campaign
            ->fetch($this->uri->segment(2));

        if( !$this->uri->segment(4) ) {
            show_error('Campaign token is required');
            return;
        }

        $donation = $this->giving_impact->donation;
        if( $this->uri->segment(6) != 'new' ) {
            $donation = $this->giving_impact
                ->donation
                ->fetch($this->uri->segment(6));
        }

        $opportunity = $this->giving_impact
            ->opportunity
            ->fetch($this->uri->segment(4));

        if( $_POST ) {
            if( !$this->form_validation->run('donation') ) {
                $this->load->view('opportunities/donation_edit.php', array(
                    'campaign' => $campaign,
                    'opportunity' => $opportunity,
                    'donation' => $donation
                ));
                return;
            }

            $date = strtotime(sprintf(
                "%s %s %s",
                $this->input->post('month'),
                $this->input->post('day'),
                $this->input->post('year')
            ));

            $offline = true;
            if( $donation->id_token && !$donation->offline ) {
                $offline = false;
            }

            $data = array(
                'first_name'        => $this->input->post('first_name'),
                'last_name'         => $this->input->post('last_name'),
                'offline'           => $offline,
                'billing_address1'  => $this->input->post('street'),
                'billing_city'      => $this->input->post('city'),
                'billing_state'     => $this->input->post('state'),
                'billing_postal_code'=> $this->input->post('zip'),
                'billing_country'   => $this->input->post('country'),
                'donation_total'    => $this->input->post('amount') * 100,
                'email_address'     => $this->input->post('email'),
                'contact'           => $this->input->post('contact') ? true : false,
                'donation_date'     => date('Y-m-d H:i:s', $date)
            );

            if( !$donation->id_token ) {
                $data['opportunity'] = $opportunity->id_token;
            }

            if( $this->input->post('donation_level') ) {
                $label = '';
                foreach( $campaign->donation_levels as $lvl ) {
                    if( $lvl->level_id == $this->input->post('donation_level') ) {
                        $label = $lvl->label;
                        break;
                    }
                }

                $data['donation_level'] = $label;
                $data['donation_level_id'] = $this->input->post('donation_level');
            } else {
                $data['donation_level'] = false;
                $data['donation_level_id'] = false;
            }

            if( $this->input->post('fields') ) {
                $data['custom_responses'] = $this->input->post('fields');
            }

            try {
              if( $donation->id_token ) {
                  foreach( $data as $k => $v ) {
                      $donation->$k = $v;
                  }
                  $donation->save();
              } else {
                  $res = $this->giving_impact
                      ->donation
                      ->create($data);
              }
            } catch( Exception $e ) {
                $this->session->set_flashdata('message', $e->getMessage());
                $this->session->set_flashdata('message_type', 'alert');

                if( $donation->id_token ) {
                    redirect(site_url('campaigns/'.$campaign->id_token.'/opportunities/'.$opportunity->id_token.'/donations/'.$donation->id_token.'/edit'), 'header');
                } else {
                    redirect(site_url('campaigns/'.$campaign->id_token.'/opportunities/'.$opportunity->id_token.'/donations/new'), 'header');
                }
                return;
            }

            $this->session->set_flashdata('message', 'Donation updated.');
            redirect(site_url('campaigns/'.$campaign->id_token.'/opportunities/'.$opportunity->id_token.'/donations'), 'header');
            return;
        }

        $this->load->view('opportunities/donation_edit.php', array(
            'campaign' => $campaign,
            'opportunity' => $opportunity,
            'donation' => $donation
        ));
    }

    /**
     * Cancel handler
     */
    public function donation_cancel() {
        if( !$this->uri->segment(2) ) {
            show_error('Campaign token is required');
            return;
        }

        $donation = $this->Donation_model
            ->where('donation_token', $this->uri->segment(6))
            ->limit(1)
            ->find();

        if (count($donation)) {
            $donation = $donation[0];
        } else {
            show_error('Donation not found');
            return;
        }

        $campaign = $this->giving_impact
            ->campaign
            ->fetch($this->uri->segment(2));

        $opportunity = $donation->opportunity;

        $account = $this->Account_model
            ->where('id', 1)
            ->find();

        if ($this->input->post('d')) {
            $cust = Stripe_Customer::retrieve(
                $donation->stripe_charge_id,
                $this->config->item('stripe_secret_key')
            );

            $subs = $cust->subscriptions->all(array('limit' => 100));

            if (!$subs->data) {
                show_error('Unable to find subscription');
                return;
            }

            $stripe_subscription = false;
            foreach ($subs->data as $sub) {
                if ($sub->plan->id == 'tt'.$donation->plan_id) {
                    $stripe_subscription = $sub;
                }
            }

            if ($stripe_subscription === false) {
                show_error('Unable to find subscription for plan');
                return;
            }

            $stripe_plan = Stripe_Plan::retrieve(
                'tt'.$donation->plan_id,
                $this->config->item('stripe_secret_key')
            );

            if (!$stripe_plan) {
                show_error('Unable to load stripe plan');
                return;
            }

            //
            // TODO: Change this to client lib method upon upgrade
            //
            // subscription::cancel is, unfortunately, not in this library version
            $url = 'https://api.stripe.com/v1/customers/'.$donation->stripe_charge_id.'/subscriptions/'.$stripe_subscription->id;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_USERPWD, $this->config->item('stripe_secret_key'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            $result = json_decode($result);
            curl_close($ch);

            if ($result && $result->error) {
                show_error($result->error->message);
                return;
            }
            //
            // END hacky hacktown
            //

            // if (!$stripe_subscription->cancel()) {
            //     show_error('There was a problem canceling your subscription');
            //     return;
            // }

            if (!$stripe_plan->delete()) {
                show_error('There was a problem removing your plan');
                return;
            }

            $txn = new Transaction_model;
            $txn->donation_id         = $donation->id;
            $txn->type                = 'subscription_canceled';
            $txn->amount              = 0;
            $txn->stripe_id           = '';
            $txn->save_entry();

            $this->db->update(
                'donations', array('canceled' => 1), array('id' => $donation->id)
            );

            $obj = $opportunity;
            if (!$obj) {
                $obj = $campaign;
            }
            notify_subscription_canceled($donation, $obj, $account[0]);

            $this->session->set_flashdata('message', 'Donation canceled.');
        }

        redirect(site_url('campaigns/'.$campaign->id_token.'/opportunities/'.$opportunity->campaign_token.'/donations'), 'header');
        return;
    }


    /**
     * Refund handler
     */
    public function donation_refund() {
        if( !$this->uri->segment(2) ) {
            show_error('Campaign token is required');
            return;
        }

        $donation = $this->Donation_model
            ->where('donation_token', $this->uri->segment(6))
            ->find();

        if( !count($donation) ) {
            $this->session->set_flashdata('message', 'Unable to process your request.');
            $this->session->set_flashdata('message_type', 'alert');

            redirect(site_url('campaigns/'.$this->uri->segment(2)), 'header');
            return;
        }

        $donation = $donation[0];

        $opportunity = $this->Opportunity_model
            ->where('opportunity_token', $this->uri->segment(4))
            ->find();

        if( !count($opportunity) ) {
            $this->session->set_flashdata('message', 'Unable to process your request.');
            $this->session->set_flashdata('message_type', 'alert');

            redirect(site_url('campaigns/'.$this->uri->segment(2)), 'header');
            return;
        }

        $opportunity = $opportunity[0];

        $campaign = $this->Campaign_model
            ->where('campaign_token', $this->uri->segment(2))
            ->find();

        if( !count($campaign) ) {
            $this->session->set_flashdata('message', 'Unable to process your request.');
            $this->session->set_flashdata('message_type', 'alert');

            redirect(site_url('campaigns'), 'header');
            return;
        }

        $campaign = $campaign[0];

        if( !count($_POST) ) {

            $this->session->set_flashdata('message', 'Unable to process your request.');
            $this->session->set_flashdata('message_type', 'alert');

            redirect(site_url('campaigns/'.$campaign->campaign_token.'/opportunities/'.$opportunity->opportunity_token.'/donations/'.$donation->donation_token), 'header');
            return;
        }

        $charge_id = $donation->stripe_charge_id;

        $account = $this->Account_model
            ->where('id', $campaign->account_id)
            ->find();

        try {
            $charge = Stripe_Charge::retrieve(
                $donation->stripe_charge_id,
                $this->config->item('stripe_secret_key')
            );
            $refund = $charge->refund();
        } catch( Exception $e ) {
            $this->session->set_flashdata('message', $e->getMessage());
            $this->session->set_flashdata('message_type', 'alert');
            redirect(site_url('campaigns/'.$campaign->campaign_token.'/opportunities/'.$opportunity->opportunity_token.'/donations/'.$donation->donation_token), 'header');
            return;
        }

        if( $refund->id ) {
            $donation->refunded = 1;
            $donation->save_entry();

            $txn = new Transaction_model;
            $txn->donation_id         = $donation->id;
            $txn->stripe_id           = $refund->id;
            $txn->type                = 'refund';
            $txn->total               = $refund->amount;
            $txn->refunded            = 1;
            $txn->save_entry();
        }

        $campaign->current = $campaign->current - $donation->donation_total;
        $campaign->total_donations = $campaign->total_donations - 1;
        $campaign->save_entry();
        if( $opportunity ) {
            $opportunity->current = $opportunity->current - $donation->amount;
            $opportunity->total_donations = $opportunity->total_donations - 1;
            $opportunity->save_entry();
        }

        if( $donation->supporter ) {
            $donation->supporter->save_entry();
        }


        $this->session->set_flashdata('message', 'Donation refunded.');
        redirect(site_url('campaigns/'.$campaign->campaign_token.'/opportunities/'.$opportunity->opportunity_token.'/donations/'.$donation->donation_token), 'header');
        return;
    }

    /**
     * Export handler
     *
     * Exports donation log to CSV directly to the browser
     *
     */
    public function export() {
        if( !$this->uri->segment(2) ) {
            show_error('Campaign token is required');
            return;
        }

        $campaign = $this->giving_impact
            ->campaign
            ->fetch($this->uri->segment(2));

        if( !$this->uri->segment(4) ) {
            show_error('Campaign token is required');
            return;
        }

        $opportunity = $this->giving_impact
            ->opportunity
            ->fetch($this->uri->segment(4));

        $donations = $opportunity
            ->donations
            ->fetch();

        $this->output
            ->set_header('Expires: Mon, 26 Jul 1997 05:00:00 GMT') // Date in the past
            ->set_header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT') // always modified
            ->set_header('Cache-Control: cache, must-revalidate') // HTTP/1.1
            ->set_header('Pragma: public') // HTTP/1.0
            ->set_header(sprintf(
                'Content-Disposition: attachment; filename="%s"',
                url_title($campaign->title).'.csv'
            ))
            ->set_content_type('csv')
            ->set_output(donations_to_csv($campaign, $donations));
    }

     /**
     * Exports opportunities list to CSV
     *
     */
    public function go_export() {
        if( !$this->uri->segment(2) ) {
            show_error('Campaign token is required');
            return;
        }

        $campaign = $this->giving_impact
            ->campaign
            ->fetch($this->uri->segment(2));

        $opportunities = $campaign
            ->opportunities
            ->status('both')
            ->sort('title')
            ->fetch();

        $this->output
            ->set_header('Expires: Mon, 26 Jul 1997 05:00:00 GMT') // Date in the past
            ->set_header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT') // always modified
            ->set_header('Cache-Control: cache, must-revalidate') // HTTP/1.1
            ->set_header('Pragma: public') // HTTP/1.0
            ->set_header(sprintf(
                'Content-Disposition: attachment; filename="%s"',
                url_title($campaign->title).'_giving_opportunties.csv'
            ))
            ->set_content_type('csv')
            ->set_output(opportunities_to_csv($campaign, $opportunities));
    }

    /**
    * Supporters view method
    *
    * Loads 'opportunities/supporters' view with
    *
    * * campaign Campaign_model
    * * opportunity Opportunity_model
    *
    */
    public function supporters() {
        if( !$this->uri->segment(2) ) {
            show_error('Campaign token is required');
            return;
        }

        $campaign = $this->giving_impact
            ->campaign
            ->fetch($this->uri->segment(2));

        if( !$this->uri->segment(4) ) {
            show_error('Campaign token is required');
            return;
        }

        $opportunity = $this->giving_impact
            ->opportunity
            ->fetch($this->uri->segment(4));

        if( $this->input->post('supporter_email_address') ) {
            $addr = $this->input->post('supporter_email_address');

            $supporter = $this->Supporter_model
                ->where('email_address', $addr)
                ->find();

            if( !count($supporter) ) {
                redirect(site_url('supporters/new').'?opportunity='.$opportunity->id_token.'&email='.$addr, 'header');
                return;
            }


            $opportunity->supporters[] = array('email_address' => $addr);
            $opportunity->save();

            $this->session->set_flashdata('message', 'Supporter added.');
            redirect(site_url($this->uri->uri_string()), 'header');
            return;
        }

        $this->load->view('opportunities/supporters.php', array(
            'campaign' => $campaign,
            'opportunity' => $opportunity
        ));

    }

    public function supporters_remove() {
        if( !$this->uri->segment(2) ) {
            show_error('Campaign token is required');
            return;
        }

        $campaign = $this->giving_impact
            ->campaign
            ->fetch($this->uri->segment(2));

        if( !$this->uri->segment(4) ) {
            show_error('Campaign token is required');
            return;
        }

        $opportunity = $this->giving_impact
            ->opportunity
            ->fetch($this->uri->segment(4));

        $supporter = $this->Supporter_model
            ->where('supporter_token', $this->uri->segment(6))
            ->find();


        if( $this->input->post('s') == 1 && count($supporter) ) {
            $supporters = array();
            foreach( $opportunity->supporters as $supp ) {
                if( $supp->id_token != $supporter[0]->supporter_token ) {
                    $supporters[] = $supp;
                }
            }

            $opportunity->supporters = $supporters;
            $opportunity->save();

            $this->session->set_flashdata('message', 'Supporter removed.');
        }

        redirect(site_url('campaigns/'.$campaign->id_token.'/opportunities/'.$opportunity->id_token.'/supporters'), 'header');
        return;

    }

}
