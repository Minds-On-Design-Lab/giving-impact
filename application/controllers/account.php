<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'/libraries/stripe/Stripe.php');

/**
 * Account controller
 *
 * @class Account
 * @extends  CI_controller
 * @copyright  2013 Minds On Design Lab
 * @author  Mike Joseph <mikej@mod-lab.com>
 */
class Account extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->library('auth');
        $this->load->library('giving_impact');
        $this->load->library('form_validation');

        $this->load->model('account_model');
        $this->load->model('user_model');

        $this->load->helper('notification');

        if( !$this->auth->is_authorized() && $this->uri->segment(2) !== 'connect' ) {
            $this->auth->handle_expired();
            return;
        }

        Stripe::setApiKey($this->config->item('stripe_secret_key'));

    }

    /**
     * Main action
     *
     * Calls 'account/index' with:
     *  * account Account_model
     *  * user User_model
     *  * customer Object Stripe_Customer
     *
     */
	public function index() {
	    $account = $this->account_model
	        ->where('id', $this->session->userdata('account_id'))
	        ->find();

	    $user = $this->user_model
	        ->where('id', $this->session->userdata('user_id'))
	        ->find();

        $currencies = array();
        $stripe_account = false;
        if( $this->config->item('stripe_secret_key') ) {
            Stripe::setApiKey($this->config->item('stripe_secret_key'));
            $stripe_account = Stripe_Account::retrieve();
            $currencies = $stripe_account->currencies_supported;
        }

	    $this->load->view('account/index', array(
	        'account'   => $account[0],
	        'user'      => $user[0],
            'currencies'=> $currencies,
            'stripe_account' => $stripe_account
	    ));
	}

    public function edit_currency() {
        $account = $this->account_model
            ->where('id', $this->session->userdata('account_id'))
            ->find();

        if( $_POST ) {
            $account = $account[0];

            if( $this->input->post('currency', false) && !$account->currency ) {
                $account->currency = $this->input->post('currency');
            }

            $account->save_entry();

            $this->session->set_flashdata('message', 'Account updated');
        }

        redirect(site_url('account'));
        return;

    }
    /**
     * Edit organization settings.
     *
     * Calls 'account/edit/org' with:
     *  * account Account_model
     *
     */
	public function edit_org() {
	    $account = $this->account_model
	        ->where('id', $this->session->userdata('account_id'))
	        ->find();

        if( $_POST ) {
            $account = $account[0];

            $account->account_name       = $this->input->post('account_name');
            $account->street_address     = $this->input->post('street_address');
            $account->street_address_2   = $this->input->post('street_address_2');
            $account->city               = $this->input->post('city');
            $account->state              = $this->input->post('state');
            $account->mailing_postal_code= $this->input->post('mailing_postal_code');

            if( $_FILES && count($_FILES) ) {
                $file = $_FILES['file'];
				$account->set_file($file['tmp_name'], $file['type']);
            }

            if( $this->input->post('currency', false) && !$account->currency ) {
                $account->currency = $this->input->post('currency');
            }

            $account->save_entry();

            $this->session->set_flashdata('message', 'Account updated');
            redirect(site_url('account'));
            return;
        }


	    $this->load->view('account/edit/org', array(
	        'account'   => $account[0],
	    ));

	}

    /**
     * Edit user settings/
     *
     * Calls 'account/edit/user' with:
     *  * account Account_model
     *  * user User_model
     *
     */
	public function edit_user() {
	    $account = $this->account_model
	        ->where('id', $this->session->userdata('account_id'))
	        ->find();

	    $user = $this->user_model
	        ->where('id', $this->session->userdata('user_id'))
	        ->find();

        $timezones = include_once __DIR__.'/../libraries/timezones.php';

        if( $_POST ) {
            $account = $account[0];
            $user = $user[0];

            $first_name     = $this->input->post('first_name');
            $last_name      = $this->input->post('last_name');
            $email          = $this->input->post('email');
            $tz             = $this->input->post('timezone');

            $pass           = $this->input->post('pass');
            $pass2          = $this->input->post('pass2');

            if( !$this->form_validation->run('user_edit') ) {
                $this->load->view('account/edit/user', array(
                    'account'   => $account,
                    'user'      => $user
                ));
                return;
            }

            $account->first_name    = $first_name;
            $account->last_name     = $last_name;
            $account->timezone      = $tz;
            $account->save_entry();

            if( $email != $user->email ) {
                $customer = Stripe_Customer::retrieve($account->stripe_id);
                $customer->email = $email;
                $customer->save();
            }

            $user->email            = $email;
            if( $pass && $pass == $pass2 ) {
                $user->password_salt        = $this->auth->generate_salt();
                $user->crypted_password     = $this->auth->hash_pass(
                    $pass, $user->password_salt
                );
            }

            $user->save_entry();

            $this->session->set_flashdata('message', 'User updated');
            redirect(site_url('account'));
            return;

        }

	    $this->load->view('account/edit/user', array(
	        'account'   => $account[0],
	        'user'      => $user[0],
            'timezones' => $timezones
	    ));

	}

    /**
     * Allows user to regenerate API tokens
     *
     */
	public function edit_token() {

        $user = $this->user_model
	        ->where('id', $this->session->userdata('user_id'))
	        ->find();

        if( $_POST ) {
            $user[0]->single_access_token = false;
            $user[0]->public_access_token = false;
            $user[0]->save_entry();

            // reset the API key for the session.
            $this->config->set_item('gi-api-key', $user[0]->single_access_token);
            $this->session->set_userdata('api_key', $user[0]->single_access_token);

            $this->session->set_flashdata('message', 'API key regenerated');
            redirect(site_url('account'));
            return;
        }

	    $this->load->view('account/edit/token');

	}

}
