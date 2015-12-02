<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * User model
 *
 * @class User_model
 * @extends  GI_Model
 */
class User_model extends GI_Model {

    public $id                  = false;
    public $email               = false;
    public $crypted_password    = false;
    public $password_salt       = false;
    public $persistence_token   = false;
    public $login_count         = 0;
    public $failed_login_count  = 0;
    public $last_request_at     = false;
    public $current_login_at    = false;
    public $last_login_at       = false;
    public $current_login_ip    = false;
    public $last_login_ip       = false;
    public $account_id          = false;
    public $perishable_token    = false;
    public $single_access_token = false;
    public $active              = 1;
    public $change_request      = false;
    public $public_access_token = false;

	public function __construct() {
		parent::__construct();

	}

    /**
     * Overrides parent save_entry by setting
     *
     *  * perishable_token
     *  * single_access_token
     *  # persistence_token
     *
     * @return Object this
     */
	public function save_entry() {

        if( !$this->perishable_token ) {
            $this->perishable_token = sha1(uniqid());
        }

        if( !$this->single_access_token ) {
            $this->single_access_token = sha1(uniqid());
        }

        if( !$this->persistence_token ) {
            $this->persistence_token = sha1(uniqid());
        }

        if( !$this->public_access_token ) {
            $this->public_access_token = sha1(uniqid());
        }

		parent::save_entry();

		return $this;
	}

    public function __account() {
        $this->load->model('Account_model');

        $acct = $this->Account_model
            ->where('id', $this->account_id)
            ->find();

        if( !count($acct) ) {
            return false;
        }

        return $acct[0];
    }

}