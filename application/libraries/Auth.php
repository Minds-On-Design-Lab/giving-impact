<?php

class Auth {

    private $CI = false;

    private $user = false;

    public function __construct() {
        $this->CI = get_instance();

        if( $this->is_authorized() ) {
            date_default_timezone_set($this->authorized_user()->account->timezone);
            $this->config->set_item('gi-api-key', $this->session->userdata('api_key'));
        }
    }

    public function generate_salt() {
        $tok = substr(sha1(time().rand(0,99)), 0, 10);

        if( !preg_match('/[^0-9]/', $tok) ) {
            $alpha = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $letter = $alpha{(rand(0,strlen($alpha)-1))};

            $split = rand(0, strlen($tok)-2);

            $tok = sprintf(
                '%s%s%s',
                substr($tok, 0, $split),
                $letter,
                substr($tok, $split+1)
            );
        }

        return $tok;
    }

    public function verify($email, $pass) {

        $user = $this->CI->db
            ->where('email', $email)
            ->limit(1)
            ->get('users')
            ->result();
        if( !count($user) ) {
            return false;
        }

        $pw = hash('sha512', $pass.$user[0]->password_salt);

        for($i=0; $i<19; $i++ ) {
            $pw = hash('sha512', $pw);
        }

        if( $user[0]->crypted_password == $pw ) {
            return $user[0];
        }

        return false;
    }

    public function hash_pass($pass, $salt) {

        $pw = hash('sha512', $pass.$salt);

        for($i=0; $i<19; $i++ ) {
            $pw = hash('sha512', $pw);
        }

        return $pw;
    }

    public function is_authorized() {
        return $this->session->userdata('authenticated') === 1;
    }

	public function authorize_user($user) {

        $account = $this->CI->db
            ->where('id',$user->account_id)
            ->limit(1)
            ->get('accounts')
            ->result();

		$data = array(
			'user_id' => $user->id,
			'account_id' => $user->account_id,
			'authenticated' => 1,
			'api_key' => $user->single_access_token,
		);

		$this->CI->session->set_userdata($data);

        $this->config->set_item('gi-api-key', $this->session->userdata('api_key'));
	}

	public function authorized_user() {
	    if( !$this->is_authorized() ) {
	        return false;
	    }

        if( $this->user ) {
            return $this->user;
        }

	    $id = $this->CI->session->userdata('user_id');

        $user = $this->CI->db
            ->where('id', $id)
            ->limit(1)
            ->get('users')
            ->result();

        if( !count($user) ) {
            return false;
        }

        $this->user = $user[0];

        $account = $this->CI->db
            ->where('id',$this->user->account_id)
            ->limit(1)
            ->get('accounts')
            ->result();

        // Following is used for Intercom data
        $this->user->first_name = $account[0]->first_name;
        $this->user->last_name = $account[0]->last_name;
        $this->user->account_name = $account[0]->account_name;
        $this->user->account = $account[0];

        $select = array(
                'sum(amount) as amount',
                'count(id) as donations'
            );
        $donations = $this->CI->db
            ->select($select)
            ->where('account_id',$this->user->account_id)
            ->get('donations')
            ->result();


        $this->user->account_amount = $donations[0]->amount;
        $this->user->account_donations = $donations[0]->donations;

        return $this->user;
	}

    public function __get($k) {
        return $this->CI->$k;
    }

    public function handle_expired() {
        $this->session->set_flashdata('last_url', $this->uri->uri_string());
        $this->session->set_flashdata('message', 'Sorry, your session expired');

        redirect(site_url('/'));
    }
}