<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'/libraries/stripe/Stripe.php');

/**
 * Main controller
 *
 * @class Main
 * @extends CI_Controller
 */
class Main extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->library('auth');
        $this->load->model('account_model');
        $this->load->model('user_model');
        $this->load->helper('notification');

        Stripe::setApiKey($this->config->item('stripe_secret_key'));
    }

    /**
     * Index handler. If the user is currently logged in, will redirect
     * to the dashboard.
     *
     * Loads 'main/index' view
     */
	public function index() {

        if( $this->auth->is_authorized() ) {
            redirect(site_url('dashboard'));
            return;
        }

        $existing = $this->account_model->order_by('id desc')->find();

        if (!$existing || !count($existing)) {
            redirect(site_url('new_account'));
            return;
        }


		$this->load->view('main/index');

		$this->session->keep_flashdata('last_url');
	}

    /**
     * Login URL callback. Accepts via POST:
     *  * email String
     *  * password String
     *
     * If valid login, will redirect to dashboard, failure will redirect
     * back to login with flashdata key 'login_message' set with the
     * error message
     *
     */
	public function login() {
	    $email = $this->input->post('email');
	    $pass = $this->input->post('password');

	    if( ($u = $this->auth->verify($email, $pass)) ) {
	        $this->auth->authorize_user($u);
	        $this->session->set_flashdata(
	            'message',
	            'Welcome'
	        );

	        if( ($last = $this->session->flashdata('last_url')) ) {
	            redirect($last);
	            return;
	        }

		    redirect(site_url('dashboard'));
        } else {
    		$this->session->keep_flashdata('last_url');
	        $this->session->set_flashdata(
	            'login_message',
	            'Incorrect email/password combination'
	        );
    	    redirect(site_url('/'));
	    }

	    return;
	}

    /**
     * Logout URL callback. Redirects back to site root
     *
     */
	public function logout() {
	    $this->session->sess_destroy();

	    redirect(site_url('/'));
	    return;
	}

    /**
     * Create a new accounts. Accepts via POST:
     *
     *  * org_name String
     *  * first_name String
     *  * last_name String
     *  * email String
     *  * token String
     *  * password String
     *  * password2 String
     *
     *  If account creation errors, will load 'main/new_account' with appropriate
     *  form error. A successful creation will authenticate the user and redirect
     *  them to the dashboard.
     *
     * On initial load, loads 'main/new_account' view
     *
     */
	public function new_account() {
        if( $_POST ) {

            $existing = $this->account_model->order_by('id desc')->find();

            if ($existing && count($existing)) {
                redirect(site_url('dashboard'));
                return;
            }

            try {

                $org_name       = 'Default Account';
                $first_name     = $this->input->post('first_name');
                $last_name      = $this->input->post('last_name');
                $email          = $this->input->post('email');

                $password       = $this->input->post('pass');
                $password2      = $this->input->post('pass2');

                $existing = $this->user_model
                    ->where('email', $email)
                    ->find();

                if( count($existing) ) {
            	    $this->load->view('main/new_account', array(
            	        'form_error' => 'An account already exists with that email.'
            	    ));
                    return;
                }

                if( !$password || $password != $password2 ) {
            	    $this->load->view('main/new_account', array(
            	        'form_error' => 'Your passwords don\'t match.'
            	    ));
                    return;
                }

                $account = new Account_model;
                $account->account_name      = $org_name;
                $account->first_name        = $first_name;
                $account->last_name         = $last_name;
                $account->reply_to_email    = $email;
                $account->timezone          = 'UTC';

                $account->save_entry();

                $user = new User_model;
                $user->email                = $email;
                $user->password_salt        = $this->auth->generate_salt();
                $user->crypted_password     = $this->auth->hash_pass(
                    $password, $user->password_salt
                );
                $user->account_id           = $account->id;
                $user->signup_date          = date('Y-m-d g:i:s');
                $user->save_entry();

    	        $this->auth->authorize_user($user);

                // notify_signup($user, $account);

    	        $this->session->set_flashdata(
    	            'message',
    	            'Welcome to Giving Impact '.$first_name
    	        );

    	        redirect(site_url('dashboard'));
    	        return;
            } catch (Exception $e) {
                $this->load->view('main/new_account', array(
                    'form_error' => $e->getMessage()
                ));
                return;
            }

        }

	    $this->load->view('main/new_account', array(
	        'form_error' => false
	    ));
	}

    /**
     * Forgot password handler.
     *
     * On intial load, loads 'main/forgot' view. After POSTing valid email address
     * creates new password reset request (which does not generate a new password),
     * notifies user via email and reloads the 'main/forgot' view with:
     *  * message String
     *  * type String
     *
     */
	public function forgot() {
	    $message = false;
	    $type = 'alert';

	    if( $_POST ) {

	        $users = $this->user_model
	            ->where('email', $this->input->post('email'))
	            ->limit(1)
	            ->find();

	        if( count($users) && $users[0]->id ) {
                $users[0]->change_request = sha1(uniqid());
                $users[0]->save_entry();

                $account = $this->account_model
                    ->where('id', $users[0]->account_id)
                    ->find();

                notify_lost_password($users[0], $account[0]);

                $message = "We've sent password reset instructions to your email address.";
                $type = "success";
	        } else {
	            $message = "Sorry, we couldn't find that email address";
	        }
	    }
	    $this->load->view('main/forgot', array(
            'message' => $message,
            'type' => $type
	    ));
	}

    /**
     * Handles the verification of the password change requests. Accepts
     * the change requests HASH as the second URL segment.
     *
     * Loads 'main/reset' view with
     *  * message String
     *  * type String
     *  * hash String
     *
     * After successfully POSTing form, password is reset and user is redirected
     * back to login form. Unsuccessful change request is brought back to the form
     * with the appropriate error set
     *
     */
	public function forgot_verify() {
	    $hash       = $this->uri->segment(2);
	    $message    = false;
	    $type       = 'alert';

	    if( !$hash ) {
	        show_error('No hash found');
	        return;
	    }

	    $users = $this->user_model
	        ->where('change_request', $hash)
	        ->find();

	    if( !count($users) ) {
            $this->auth->handle_expired();
            return;
	    }

	    if( $_POST ) {
	        if( $users[0]->email != $this->input->post('email') ) {
	            $message = "The address you provided doesn't match our records.";
	        }

	        if( $this->input->post('pass') != $this->input->post('pass2')
                || !$this->input->post('pass')
            ) {
                $message = "Your passwords didn't match";
            }

            if( $message ) {
                $this->load->view('main/reset', array(
                    'message' => $message,
                    'type' => $type,
                    'hash' => $hash
                ));
                return;
            }

            $users[0]->password_salt        = $this->auth->generate_salt();
            $users[0]->crypted_password     = $this->auth->hash_pass(
                $this->input->post('pass'), $users[0]->password_salt
            );
            $users[0]->change_request       = '';
            $users[0]->save_entry();

	        $this->session->set_flashdata(
	            'message',
	            'Password reset'
	        );
    	    redirect(site_url('/'));

	    }

	    $this->load->view('main/reset', array(
            'message' => $message,
            'type' => $type,
            'hash' => $hash
	    ));
	}
}
