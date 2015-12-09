<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'/libraries/stripe/Stripe.php');

/**
 * Campaigns Controller
 *
 * @class Campaigns
 * @extends CI_Controller
 */
class Campaigns extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->library('auth');
        $this->load->library('giving_impact');
        $this->load->library('form_validation');

        $this->load->helper('number');
        $this->load->helper('file');
        $this->load->helper('export');
        $this->load->helper('typography');

        $this->load->model('Transaction_model');
        $this->load->model('Campaign_model');
        $this->load->model('Opportunity_model');
        $this->load->model('Donation_model');
        $this->load->model('Account_model');

        if( !$this->auth->is_authorized() ) {
            $this->auth->handle_expired();
            return;
        }
    }

    public function index() {

    }

    /**
     * View single campaign
     *
     * Loads 'campaigns/view' with:
     *  * campaign Campaign_model
     *  * stats Stats_model
     *  * previous String
     *  * next String
     */
    public function view() {
        if( !$this->uri->segment(2) ) {
            show_error('Campaign token is required');
            return;
        }

        $offset = $this->uri->segment(3) ? $this->uri->segment(3) : 0;
        $max = 10;

        $campaign = $this->giving_impact
            ->campaign
            ->fetch($this->uri->segment(2));

        $stats = $campaign
            ->stats
            ->limit($max+1)
            ->offset($offset)
            ->fetch();

        $prev = $next = 'campaigns/'.$campaign->id_token;

        if( count($stats) > $max ) {
            array_pop($stats);
            $next .= '/'.($max+$offset);
        } else {
            $next = false;
        }

        if( !$offset ) {
            $prev = false;
        } else {
            $prev .= '/'.($offset-$max);
        }

        $this->load->view('campaigns/view.php', array(
            'campaign' => $campaign,
            'stats' => $stats,
            'previous' => $prev,
            'next' => $next
        ));

    }

    /**
     * Edit basic campaign settings
     *
     * Loads 'campaigns/edit/basic' with:
     *  * campaign Campaign_model
     */
    public function edit_basic() {
        if( !$this->uri->segment(2) ) {
            show_error('Campaign token is required');
            return;
        }

        $campaign = $this->giving_impact
            ->campaign
            ->fetch($this->uri->segment(2));


        if( count($_POST) && $this->form_validation->run('campaign_basic') ) {

            $campaign->title          = $this->input->post('title');
            $campaign->donation_target = $this->input->post('target') * 100;
            $campaign->donation_minimum = $this->input->post('minimum') * 100;
            $campaign->description    = $this->input->post('description');
            $campaign->status         = $this->input->post('status') ? true : false;
            $campaign->has_giving_opportunities = $this->input->post('type') ? true : false;
            $campaign->frequency_type   = $this->input->post('frequency') ? $this->input->post('frequency') : 0;
            $campaign->frequency_period = $this->input->post('interval') ? $this->input->post('interval') : 0;

            // campaign levels
            if( $this->input->post('levels') ) {
                $campaign->enable_donation_levels = true;
            } else {
                $campaign->enable_donation_levels = false;
            }

            $levels = array();

            $amounts = $this->input->post('level_amounts');
            $labels = $this->input->post('level_labels');
            $level_ids = $this->input->post('level_level_ids');


            if( is_array($amounts) && is_array($labels) && is_array($level_ids)) {
                for( $i=0; $i<count($amounts); $i++ ) {
                    $levels[] = array(
                        'amount'    => $amounts[$i] * 100,
                        'label'     => $labels[$i],
                        'level_id'  => $level_ids[$i]
                    );
                }

                $campaign->donation_levels = $levels;
            }

            if( !count($levels) && $campaign->enable_donation_levels ) {
                $this->session->set_flashdata(array(
                        'message' => 'You selected donation levels, but did not specify any levels',
                        'message_type' => 'alert'
                ));
                redirect(site_url('campaigns/'.$campaign->id_token.'/edit/basic'), 'header');
                return;
            }

            try {
                $res = $campaign
                  ->save();
            } catch( \MODL\GivingImpact\Exception $e ) {
                $this->session->set_flashdata(array(
                        'message' => $e->getMessage(),
                        'message_type' => 'alert'
                ));
                redirect(site_url('campaigns/'.$campaign->id_token.'/edit/basic'), 'header');
                return;
            }

            $this->session->set_flashdata('message', 'Campaign Updated.');
            redirect(site_url('campaigns/'.$campaign->id_token.'/edit'), 'header');
            return;
        }

        $this->load->view('campaigns/edit/basic', array(
            'campaign' => $campaign
        ));
    }

    /**
     * Edit campaign design settings
     *
     * Loads 'campaigns/edit/design' view with:
     *  * campaign Campaign_model
     */
    public function edit_design() {
        if( !$this->uri->segment(2) ) {
            show_error('Campaign token is required');
            return;
        }

        $campaign = $this->giving_impact
            ->campaign
            ->fetch($this->uri->segment(2));

        if( count($_POST) ) {

            $campaign->youtube_id     = $this->input->post('youtube');
            $campaign->analytics_id   = $this->input->post('analytics_id');

            // files
            if( $_FILES && count($_FILES) ) {
                $file = $_FILES['file'];
                $type = $file['type'];
                $path = $file['tmp_name'];

                if( $path ) {
                    $campaign->image_type = $type;
                    $campaign->image_file = base64_encode(file_get_contents($path));
                }
            }

            if( $this->input->post('remove-image') ) {
                $campaign->image_type = 'nil';
                $campaign->image_file = false;
            }

            $campaign->display_donation_target  = $this->input->post('display_donation_target') ? true : false;
            $campaign->display_donation_total   = $this->input->post('display_donation_total') ? true : false;

            try {
                $res = $campaign
                  ->save();
            } catch( \MODL\GivingImpact\Exception $e ) {
                $this->session->set_flashdata(array(
                        'message' => $e->getMessage(),
                        'message_type' => 'alert'
                ));
                redirect(site_url('campaigns/'.$campaign->id_token.'/edit/design'), 'header');
                return;
            }

            $this->session->set_flashdata('message', 'Campaign Updated.');
            redirect(site_url('campaigns/'.$campaign->id_token.'/edit'), 'header');
            return;
        }

        $this->load->view('campaigns/edit/design', array(
            'campaign' => $campaign
        ));

    }

    /**
     * Edit campaign receipt settings
     *
     * Loads 'campaigns/edit/receipt' view with:
     *  * campaign Campaign_model
     */
    public function edit_receipt() {
        if( !$this->uri->segment(2) ) {
            show_error('Campaign token is required');
            return;
        }

        $campaign = $this->giving_impact
            ->campaign
            ->fetch($this->uri->segment(2));

        if( count($_POST) ) {

            $campaign->receipt->send_receipt   = $this->input->post('send_receipt') ? true : false;

            if( $campaign->receipt->send_receipt && !$this->form_validation->run('campaign_receipt') ) {
                $this->load->view('campaigns/edit/receipt', array(
                    'campaign' => $campaign
                ));
                return;
            }

            $campaign->receipt->email_org_name = $this->input->post('email_org_name');
            $campaign->receipt->reply_to_address = $this->input->post('reply_to_address');
            $campaign->receipt->bcc_address    = $this->input->post('bcc_address');
            $campaign->receipt->street_address = $this->input->post('street_address');
            $campaign->receipt->street_address_2 = $this->input->post('street_address_2');
            $campaign->receipt->city           = $this->input->post('city');
            $campaign->receipt->state          = $this->input->post('state');
            $campaign->receipt->postal_code    = $this->input->post('zip');
            $campaign->receipt->country        = $this->input->post('country');
            $campaign->receipt->receipt_body   = $this->input->post('receipt_body');

            try {
                $res = $campaign
                  ->save();
            } catch( \MODL\GivingImpact\Exception $e ) {
                $this->session->set_flashdata(array(
                        'message' => $e->getMessage(),
                        'message_type' => 'alert'
                ));
                redirect(site_url('campaigns/'.$campaign->id_token.'/edit/receipt'), 'header');
                return;
            }

            $this->session->set_flashdata('message', 'Campaign Updated.');
            redirect(site_url('campaigns/'.$campaign->id_token.'/edit'), 'header');
            return;
        }

        $this->load->view('campaigns/edit/receipt', array(
            'campaign' => $campaign
        ));

    }

    /**
     * Edit campaign custom fields
     *
     * Loads 'campaigns/edit/fields' view with
     *  * campaign Campaign_model
     *
     * Note that this view requires the Handlebars.js library in order to
     * function properly.
     */
    public function edit_fields() {
        if( !$this->uri->segment(2) ) {
            show_error('Campaign token is required');
            return;
        }

        $campaign = $this->giving_impact
            ->campaign
            ->fetch($this->uri->segment(2));

        if( count($_POST) ) {

            // custom fields
            $custom_fields = array();
            $i = 0;
            $c_field_ids = $this->input->post('custom_field_ids');
            $c_field_types = $this->input->post('custom_field_types');
            $c_field_labels = $this->input->post('custom_field_labels');
            $c_field_options = $this->input->post('custom_field_options');
            $c_field_status = $this->input->post('custom_field_status');
            $c_field_required = $this->input->post('custom_field_required');

            foreach( $this->input->post('custom_field_ids') as $id ) {
                $fields = array(
                    'field_id'      => $id,
                    'field_type'    => $c_field_types[$i],
                    'field_label'   => $c_field_labels[$i],
                    'options'       => '',
                    'position'      => ($i+1),
                    'status'        => $c_field_status[$i],
                    'required'      => $c_field_required[$i]
                );

                if( strlen($c_field_options[$i]) ) {
                    $fields['options'] = explode("\n", $c_field_options[$i]);
                }

                $custom_fields[] = $fields;
                $i++;
            }
            if( count($custom_fields) ) {
                $campaign->custom_fields = $custom_fields;
            }

            try {
                $res = $campaign
                  ->save();
            } catch( \MODL\GivingImpact\Exception $e ) {
                $this->session->set_flashdata(array(
                        'message' => $e->getMessage(),
                        'message_type' => 'alert'
                ));
                redirect(site_url('campaigns/'.$campaign->id_token.'/edit/fields'), 'header');
                return;
            }

            $this->session->set_flashdata('message', 'Campaign Updated.');
            redirect(site_url('campaigns/'.$campaign->id_token.'/edit'), 'header');
            return;
        }

        $this->load->view('campaigns/edit/fields', array(
            'campaign' => $campaign
        ));

    }

    /**
     * Edit campaign create fields
     *
     * Loads 'campaigns/edit/campaign_fields' view with
     *  * campaign Campaign_model
     *
     * Note that this view requires the Handlebars.js library in order to
     * function properly.
     */
    public function edit_campaign_fields() {
        if( !$this->uri->segment(2) ) {
            show_error('Campaign token is required');
            return;
        }

        $campaign = $this->giving_impact
            ->campaign
            ->fetch($this->uri->segment(2));

        if( count($_POST) ) {

            // custom fields
            $campaign_fields = array();
            $i = 0;
            $c_field_ids        = $this->input->post('custom_field_ids');
            $c_field_types      = $this->input->post('custom_field_types');
            $c_field_labels     = $this->input->post('custom_field_labels');
            $c_field_options    = $this->input->post('custom_field_options');
            $c_field_status     = $this->input->post('custom_field_status');
            $c_field_required   = $this->input->post('custom_field_required');

            foreach( $this->input->post('custom_field_ids') as $id ) {
                $fields = array(
                    'field_id'      => $id,
                    'field_type'    => $c_field_types[$i],
                    'field_label'   => $c_field_labels[$i],
                    'options'       => '',
                    'position'      => ($i+1),
                    'status'        => $c_field_status[$i],
                    'required'      => $c_field_required[$i]
                );

                if( strlen($c_field_options[$i]) ) {
                    $fields['options'] = explode("\n", $c_field_options[$i]);
                }

                $campaign_fields[] = $fields;
                $i++;
            }
            if( count($campaign_fields) ) {
                $campaign->campaign_fields = $campaign_fields;
            }

            try {
                $res = $campaign
                  ->save();
            } catch( \MODL\GivingImpact\Exception $e ) {
                $this->session->set_flashdata(array(
                        'message' => $e->getMessage(),
                        'message_type' => 'alert'
                ));
                redirect(site_url('campaigns/'.$campaign->id_token.'/edit/campaign_fields'), 'header');
                return;
            }

            $this->session->set_flashdata('message', 'Campaign Updated.');
            redirect(site_url('campaigns/'.$campaign->id_token.'/edit'), 'header');
            return;
        }

        $this->load->view('campaigns/edit/campaign_fields', array(
            'campaign' => $campaign
        ));

    }

    /**
     * Main edit view
     *
     * Loads 'campaigns/edit' view with
     *  * campaign Campaign_model
     */
    public function edit() {
        if( !$this->uri->segment(2) ) {
            show_error('Campaign token is required');
            return;
        }

        $campaign = $this->giving_impact
            ->campaign
            ->fetch($this->uri->segment(2));

        $this->load->library('typography');

        $this->load->view('campaigns/edit', array(
            'campaign' => $campaign
        ));

    }

    /**
     * Create new campaign
     *
     * Loads 'campaign/edit/basic' with
     *  * campaign EMPTY Campaign_model
     */
    public function new_campaign() {

        if( count($_POST) && $this->form_validation->run('campaign_basic') ) {
            $data = array(
                'title'         => $this->input->post('title'),
                'donation_target' => $this->input->post('target') * 100,
                'donation_minimum' => $this->input->post('minimum') * 100,
                'description'   => $this->input->post('description'),
                'status'        => $this->input->post('status') ? true : false,
                'has_giving_opportunities' => $this->input->post('type') ? true : false
            );

            // campaign levels
            if( $this->input->post('levels') ) {
                $data['enable_donation_levels'] = true;
                $levels = array();

                $amounts = $this->input->post('level_amounts');
                $labels = $this->input->post('level_labels');

                for( $i=0; $i<count($amounts); $i++ ) {
                    $levels[] = array(
                        'amount' => $amounts[$i] * 100,
                        'label' => $labels[$i]
                    );
                }

                $data['donation_levels'] = $levels;
            } else {
                $data['enable_donation_levels'] = false;
                $data['donation_levels'] = array();
            }

            $res = $this->giving_impact
                ->campaign
                ->create($data);

            $this->session->set_flashdata('message', 'Campaign Created.');

            redirect(site_url('campaigns/'.$res->id_token).'/edit', 'header');
            return;
        }

        $this->load->view('campaigns/edit/basic', array(
            'campaign' => $this->giving_impact->campaign
        ));
    }

    /**
     * View single donation
     *
     * Loads campaigns/donation view with
     *  * donation Donation_model
     *  * campaign Campaign_model
     */
    public function donation() {
        if( !$this->uri->segment(2) ) {
            show_error('Campaign token is required');
            return;
        }

        $donation = $this->giving_impact
            ->donation
            ->fetch($this->uri->segment(4));

        $campaign = $this->giving_impact
            ->campaign
            ->fetch($this->uri->segment(2));

        $this->load->view('campaigns/donation.php', array(
            'campaign' => $campaign,
            'donation' => $donation
        ));
    }

    /**
     * Create/edit single donation
     *
     * Loads campaigns/donation edit view with
     *  * campaign Campaign_model
     *  * donation Donation_model
     */
    public function donation_edit() {
        if( !$this->uri->segment(2) ) {
            show_error('Campaign token is required');
            return;
        }

        $donation = $this->giving_impact->donation;
        if( $this->uri->segment(4) != 'new' ) {
            $donation = $this->giving_impact
                ->donation
                ->fetch($this->uri->segment(4));
        }

        $campaign = $this->giving_impact
            ->campaign
            ->fetch($this->uri->segment(2));

        if( $_POST ) {
            if( !$this->form_validation->run('donation') ) {
                $this->load->view('campaigns/donation_edit.php', array(
                    'campaign' => $campaign,
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
                'donation_date'     => gmdate('Y-m-d H:i:s', $date)
            );

            if( !$donation->id_token ) {
                $data['campaign'] = $campaign->id_token;
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
                    redirect(site_url('campaigns/'.$campaign->id_token.'/donations/'.$donation->id_token.'/edit'), 'header');
                } else {
                    redirect(site_url('campaigns/'.$campaign->id_token.'/donations/new'), 'header');
                }
                return;
            }

            $this->session->set_flashdata('message', 'Donation saved.');
            redirect(site_url('campaigns/'.$campaign->id_token.'/donations'), 'header');
            return;
        }

        $this->load->view('campaigns/donation_edit.php', array(
            'campaign' => $campaign,
            'donation' => $donation
        ));
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
            ->where('donation_token', $this->uri->segment(4))
            ->find();

        if( !count($donation) ) {
            $this->session->set_flashdata('message', 'Unable to process your request.');
            $this->session->set_flashdata('message_type', 'alert');

            redirect(site_url('campaigns/'.$this->uri->segment(2)), 'header');
            return;
        }

        $donation = $donation[0];

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

            redirect(site_url('campaigns/'.$campaign->campaign_token.'/donations/'.$donation->donation_token), 'header');
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
            redirect(site_url('campaigns/'.$campaign->campaign_token.'/donations/'.$donation->donation_token), 'header');
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

        $campaign->current = $campaign->current - $donation->amount;
        $campaign->total_donations = $campaign->total_donations - 1;
        $campaign->save_entry();

        if( $donation->campaign_id != $donation->campaign_group_id ) {
            $opp = $this->Opportunity_model
                ->get_entry($donation->campaign_id);

            $opp->current = $opp->current - $donation->amount;
            $opp->total_donations = $opp->total_donations - 1;
            $opp->save_entry();
        }

        if( $donation->supporter ) {
            $donation->supporter->save_entry();
        }


        $this->session->set_flashdata('message', 'Donation refunded.');
        redirect(site_url('campaigns/'.$campaign->campaign_token.'/donations/'.$donation->donation_token), 'header');
        return;
    }

    /**
     * View recent donations. The final URL segment is used as the offset to
     * paginate results.
     *
     * Loads 'campaigns/donations' with:
     *  * campaign Campaign_model
     *  * donations Array of Donation_model
     *  * previous String
     *  * next String
     */
    public function donations() {
        if( !$this->uri->segment(2) ) {
            show_error('Campaign token is required');
            return;
        }

        $offset = $this->uri->segment(4) ? $this->uri->segment(4) : 0;
        $max = 20;

        $campaign = $this->giving_impact
            ->campaign
            ->fetch($this->uri->segment(2));

        $donations = $campaign
            ->donations
            ->sort('donation_date|desc')
            ->related('opportunity');
        if( $offset ) {
            $donations->offset($offset);
        }
        $donations = $donations
            ->limit(($max+1))
            ->fetch();

        $prev = $next = 'campaigns/'.$campaign->id_token.'/donations';

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


        $this->load->view('campaigns/donations.php', array(
            'campaign' => $campaign,
            'donations' => $donations,
            'previous' => $prev,
            'next' => $next
        ));
    }

    /**
     * Exports donation log to CSV
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

        $donations = $campaign
            ->donations
            ->related('opportunity')
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
}
