<?php
require_once(APPPATH.'/libraries/stripe/Stripe.php');

/**
 * Supporter controller
 *
 * @class Supporters
 * @extends CI_Controller
 */
class Supporters extends CI_Controller {

    private $required = array(
        'email_address'
    );

    private $allowed = array(
        'first_name',
        'last_name',
        'street_address',
        'city',
        'state',
        'postal_code',
        'country',
        'email_address'
    );

    public function __construct() {
        parent::__construct();

        $this->load->model('Campaign_model');
        $this->load->model('Opportunity_model');
        $this->load->model('Donation_model');
        $this->load->model('Supporter_model');

        $this->load->helper('number');
        $this->load->helper('display');
        $this->load->helper('api');
        $this->load->helper('notification');
    }

    /**
     * Handles creation of supporter, or list of all supporters
     *
     * Handles
     *
     *  * GET /api/v2/supporters
     *  * POST /api/v2/supporters
     *
     * Accepts via POST:
     *
     *  * first_name {String}
     *  * last_name {String}
     *  * street_address {String}
     *  * city {String}
     *  * state {String}
     *  * postal_code {String}
     *  * country {String}
     *  * email_address {String}
     *  * contact {Boolean}
     *
     * @return Array of Supporter_model objects
     */
    public function index() {
        if( !$this->input->post_body ) {
            if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
                $this->output->show_error('Your request body is missing or malformed', 400, 'bad_request');
                return;
            }
            $supporters = api_limit_sort($this->Supporter_model)
                ->where('account_id', $this->authorization->account)
                ->find();

            $this->load->view('v2/campaigns/supporters', array(
                'supporters' => prep_supporters($supporters)
            ));
            return;
        }

        $data = $this->input->post_body;

        $supporter = $this->_process_post(new Supporter_model);
        if( !$supporter ) {
            return;
        }

        $supporter->account_id = $this->authorization->account;

        // last thing - make sure there isn't an existing user
        $dupe = $this->Supporter_model
            ->where('account_id', $supporter->account_id)
            ->where('email_address', $supporter->email_address)
            ->find();
        if( $dupe && count($dupe) ) {
            $this->output->show_error('A supporter with this address already exists', 400, 'bad_request');
            return;
        }

        $supporter->save_entry();

        // just make sure we have all the right data
        $supporter = $this->Supporter_model
            ->get_entry($supporter->id);

        $this->load->view('v2/supporters/single', array(
            'supporter' => array_shift(prep_supporters(array($supporter)))
        ));

    }

    /**
     * Handles display and edit of single supporter
     *
     * Handles:
     *
     *  * GET /api/v2/supporters/{token}
     *  * POST /api/v2/supporters/{token}
     *
     * Accepts via GET:
     *
     *  * related {String}
     *
     * Accepts via POST:
     *
     *  * first_name {String}
     *  * last_name {String}
     *  * street_address {String}
     *  * city {String}
     *  * state {String}
     *  * postal_code {String}
     *  * country {String}
     *  * email_address {String}
     *
     * @return Object Supporter_model
     */
    public function token() {
        $supporter = $this->Supporter_model
            ->where('account_id', $this->authorization->account)
            ->where('supporter_token', $this->uri->rsegment(3))
            ->limit(1)
            ->find();

        $data = $this->input->post_body;

        if( !$data && $_SERVER['REQUEST_METHOD'] == 'POST' ) {
            $this->output->show_error('Your request body is missing or malformed', 400, 'bad_request');
            return;
        }

        if( $data ) {

            $supporter = $this->_process_post($supporter[0]);
            if( !$supporter ) {
                return;
            }

            // last thing - make sure there isn't an existing user
            $dupe = $this->Supporter_model
                ->where('account_id', $supporter->account_id)
                ->where('email_address', $supporter->email_address)
                ->where('id !=', $supporter->id)
                ->find();

            if( $dupe && count($dupe) ) {
                $this->output->show_error('A supporter with this address already exists', 400, 'bad_request');
                return;
            }

            $supporter->save_entry();

            // just make sure we have all the right data
            $supporter = $this->Supporter_model
                ->get_entry($supporter->id);
        }

        $this->load->view('v2/supporters/single', array(
            'supporter' => array_shift(prep_supporters($supporter))
        ));
    }

    public function donations() {
        $supporter = $this->Supporter_model
            ->where('account_id', $this->authorization->account)
            ->where('supporter_token', $this->uri->rsegment(3))
            ->limit(1)
            ->find();

        $donations = api_limit_sort($this->Donation_model)
            ->where('donations.supporter_id', $supporter[0]->id)
            ->where('donations.account_id', $this->authorization->account)
            ->find();

        $this->load->view('v2/campaigns/donations', array(
            'donations' => prep_donations($donations)
        ));
    }

    public function opportunities() {
        $supporter = $this->Supporter_model
            ->where('account_id', $this->authorization->account)
            ->where('supporter_token', $this->uri->rsegment(3))
            ->limit(1)
            ->find();

        $opportunities = api_limit_sort($this->Opportunity_model)
            ->join('opportunities_supporters as os', 'os.opportunity_id = campaigns.id', 'left')
            ->where('os.supporter_id', $supporter[0]->id)
            ->where('campaigns.account_id', $this->authorization->account)
            ->find();

        $this->load->view('v2/campaigns/opportunities', array(
            'opportunities' => prep_campaigns($opportunities)
        ));
    }

    private function _process_post($supporter) {
        $data = json_decode(json_encode($this->input->post_body), true);

        // sanity
        if( !$supporter->id ) {
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
            $supporter->$k = $this->security->xss_clean($v);
        }

        return $supporter;
    }
}