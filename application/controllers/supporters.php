<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Supporters controller
 *
 * @class Supporters
 * @extends CI_Controller
 */
class Supporters extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->library('auth');
        $this->load->library('giving_impact');
        $this->load->library('form_validation');

        $this->load->helper('number');
        $this->load->helper('export');

        $this->load->model('Supporter_model');

        if( !$this->auth->is_authorized() ) {
            $this->auth->handle_expired();
            return;
        }
    }

    /**
     * Index handler. Loads 'supporters/index' view with:
     *
     *  * supporters Array of Supporters_model
     *
     */
	public function index() {

        // Top Most Gracious Donors
        $topdonors = $this->giving_impact
            ->supporter
            ->sort('donations_total|desc')
            ->limit(5)
            ->fetch();

        // Top Most Active Donors
        $freqdonors = $this->giving_impact
            ->supporter
            ->sort('total_donations|desc')
            ->limit(5)
            ->fetch();

        // Full List

        $offset = $this->uri->segment(2) ? $this->uri->segment(2) : 0;
        $max = 20;

        $supporters = $this->giving_impact
            ->supporter;
        if( $offset ) {
            $supporters->offset($offset);
        }
        $supporters = $supporters
            ->sort('last_name|asc')
            ->limit(($max+1))
            ->fetch();

        $prev = $next = 'supporters';

        if( count($supporters) > $max ) {
            array_pop($supporters);
            $next .= '/'.($max+$offset);
        } else {
            $next = false;
        }

        if( !$offset ) {
            $prev = false;
        } else {
            $prev .= '/'.($offset-$max);
        }

		$this->load->view('supporters/index', array(
		    'supporters' => $supporters,
            'topdonors' => $topdonors,
            'freqdonors' => $freqdonors,
            'previous' => $prev,
            'next' => $next
		));
	}

    /**
     * View single supporter
     *
     * Loads 'supporters/supporter' view with
     *
     *  * supporter Supporter_model
     *  * donations donations Array of Donation_models
     *  * prev donation pagination
     *  * next donation pagination
     *
     */

    public function view() {
        if( !$this->uri->segment(2) ) {
            show_error('Supporter is not valid');
            return;
        }

        $supporter = $this->giving_impact
            ->supporter
            ->limit(1)
            ->fetch($this->uri->segment(2));

        // Donation List

        $offset = $this->uri->segment(3) ? $this->uri->segment(3) : 0;
        $max = 10;

        $donations = $this->giving_impact
            ->supporter
            ->donations;
        if( $offset ) {
            $supporter->donations->offset($offset);
        }

        $opportunities = $supporter
            ->opportunities
            ->sort('title')
            ->related(true)
            ->fetch();

        $donations = $supporter
            ->donations
            ->sort('donation_date|desc')
            ->limit(($max+1))
            ->related(true)
            ->fetch();

        $prev = $next = 'supporters/'.$this->uri->segment(2);

        if(count($donations) > $max ) {
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

        $this->load->view('supporters/view', array(
            'supporter' => $supporter,
            'donations' => $donations,
            'opportunities' => $opportunities,
            'previous' => $prev,
            'next' => $next
        ));
    }

    /**
     * Edit supporter
     *
     * Loads 'supporters/edit' view with
     *
     *  * supporter Supporter_model
     *
     */

    public function edit() {
        if( !$this->uri->segment(2) ) {
            show_error('Supporter is not valid');
            return;
        }

        $supporter = $this->giving_impact
            ->supporter
            ->limit(1)
            ->fetch($this->uri->segment(2));


        if( $_POST && count($_POST) ) {
            if( !$this->form_validation->run('supporter') ) {
                $this->load->view('supporters/edit', array(
                    'supporter' => $supporter
                ));
                return;
            }

            $supporter->id_token            = $supporter->id_token;
            $supporter->first_name          = $this->input->post('first_name');
            $supporter->last_name           = $this->input->post('last_name');
            $supporter->email_address       = $this->input->post('email');
            $supporter->street_address      = $this->input->post('street_address');
            $supporter->city                = $this->input->post('city');
            $supporter->state               = $this->input->post('state');
            $supporter->postal_code         = $this->input->post('postal_code');
            $supporter->country             = $this->input->post('country');

            try {
                $res = $supporter
                  ->save();
            } catch( \MODL\GivingImpact\Exception $e ) {
                $this->session->set_flashdata(array(
                        'message' => $e->getMessage(),
                        'message_type' => 'alert'
                ));
                redirect(site_url('supporters/'.$supporter->id_token.'/edit'), 'header');
                return;
            }

            $this->session->set_flashdata('message', 'Supporter Saved.');
            redirect(site_url('supporters/'.$supporter->id_token), 'header');
            return;
        }

        $this->load->view('supporters/edit', array(
            'supporter' => $supporter
        ));

    }

    public function new_supporter() {

        if( !$_POST && !count($_POST) && (!$this->input->get_post('opportunity') || !$this->input->get_post('email')) ) {
            $this->session->set_flashdata(array(
                    'message' => 'Unknown opportunity',
                    'message_type' => 'alert'
            ));
            redirect(site_url('dashboard'), 'header');
            return;
        }

        $opportunity = $this->giving_impact
            ->opportunity
            ->related(true)
            ->fetch($this->input->get_post('opportunity'));

        if( $_POST && count($_POST) ) {
            if( !$this->form_validation->run('supporter') ) {
                $address = $this->input->get('email');

                $this->load->view('supporters/new', array(
                    'opportunity'   => $opportunity,
                    'email'         => $this->input->post('email')
                ));
                return;
            }

            $opportunity->supporters[] = array('email_address' => $this->input->post('email'));
            $opportunity->save();

            $supporter = $this->Supporter_model
                ->where('email_address', $this->input->post('email'))
                ->find();

            $supporter[0]->first_name          = $this->input->post('first_name');
            $supporter[0]->last_name           = $this->input->post('last_name');
            $supporter[0]->email_address       = $this->input->post('email');
            $supporter[0]->street_address      = $this->input->post('street_address');
            $supporter[0]->city                = $this->input->post('city');
            $supporter[0]->state               = $this->input->post('state');
            $supporter[0]->postal_code         = $this->input->post('postal_code');
            $supporter[0]->country             = $this->input->post('country');

            try {
                $res = $supporter[0]
                    ->save_entry();
            } catch( \MODL\GivingImpact\Exception $e ) {
                $this->session->set_flashdata(array(
                        'message' => $e->getMessage(),
                        'message_type' => 'alert'
                ));
                redirect(site_url('dashboard'), 'header');
                return;
            }

            $this->session->set_flashdata('message', 'Supporter Saved.');
            redirect(site_url('supporters/'.$supporter[0]->supporter_token), 'header');
            return;
        }

        $address = $this->input->get('email');

        $this->load->view('supporters/new', array(
            'opportunity'   => $opportunity,
            'email'         => $address
        ));

    }
}
