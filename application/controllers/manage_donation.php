<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'/libraries/stripe/Stripe.php');
/**
 * Donate controller
 *
 * @class Donate
 * @extends CI_Controller
 */
class Manage_donation extends CI_Controller {

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
        $this->load->model('Transaction_model');
        $this->load->model('Donation_model');

        $referrer = $this->input->get('referrer');
        if( $this->session->flashdata('referrer') ) {
            $this->session->keep_flashdata('referrer');
        }
        if( $referrer && strlen($referrer) ) {
            $this->session->set_flashdata('referrer', $referrer);
        }
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
        $hash = $this->uri->segment(2);

        if (!$hash) {
            show_error('Unknown donation');
            return;
        }

        $donation = $this->Donation_model
            ->where('donation_token', $hash)
            ->find();

        if (!$donation || !$donation[0]->id) {
            show_error('Unknown donation');
            return;
        }

        $campaign = $donation[0]->campaign;
        $opportunity = $donation[0]->opportunity;

        $account = $this->Account_model
            ->where('id', 1)
            ->find();

        $this->load->view('manage_donation/index', array(
            'account'       => $account[0],
            'donation'      => $donation[0],
            'campaign'      => $opportunity ? $opportunity : $campaign,
            'is_opportunity'=> $campaign ? false : true,
            'hash'          => $this->uri->segment(2)
        ));
	}

	public function cancel($charge_error = false) {

	}


}
