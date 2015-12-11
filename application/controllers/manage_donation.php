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

        if ($this->input->post('d') && $this->input->post('d') == $hash) {
            $cust = Stripe_Customer::retrieve(
                $donation[0]->stripe_charge_id,
                $this->config->item('stripe_secret_key')
            );

            $subs = $cust->subscriptions->all(array('limit' => 100));

            if (!$subs->data) {
                show_error('Unable to find subscription');
                return;
            }

            $stripe_subscription = false;
            foreach ($subs->data as $sub) {
                if ($sub->plan->id == 'tt'.$donation[0]->plan_id) {
                    $stripe_subscription = $sub;
                }
            }

            if ($stripe_subscription === false) {
                show_error('Unable to find subscription for plan');
                return;
            }

            $stripe_plan = Stripe_Plan::retrieve(
                'tt'.$donation[0]->plan_id,
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
            $url = 'https://api.stripe.com/v1/customers/'.$donation[0]->stripe_charge_id.'/subscriptions/'.$stripe_subscription->id;
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
            $txn->donation_id         = $donation[0]->id;
            $txn->type                = 'subscription_canceled';
            $txn->amount              = 0;
            $txn->stripe_id           = '';
            $txn->save_entry();

            $this->db->update(
                'donations', array('canceled' => 1), array('id' => $donation[0]->id)
            );

            $obj = $opportunity;
            if (!$obj) {
                $obj = $campaign;
            }
            notify_subscription_canceled($donation[0], $obj, $account[0]);

            redirect(site_url('donation/'.$this->uri->segment(2).'/complete'));
        }

        $this->load->view('manage_donation/index', array(
            'account'       => $account[0],
            'donation'      => $donation[0],
            'campaign'      => $opportunity ? $opportunity : $campaign,
            'is_opportunity'=> $campaign ? false : true,
            'hash'          => $this->uri->segment(2)
        ));
	}

    public function complete() {
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

        $this->load->view('manage_donation/complete', array(
            'account'       => $account[0],
            'donation'      => $donation[0],
            'campaign'      => $opportunity ? $opportunity : $campaign,
            'is_opportunity'=> $campaign ? false : true,
            'hash'          => $this->uri->segment(2)
        ));

    }
}
