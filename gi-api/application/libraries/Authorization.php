<?php

/**
 * Authorization library
 *
 * @class Authorization
 */
class Authorization {

	private $CI = false;

	private $auth = false;

	public function __construct() {
		$this->CI = get_instance();

		$ua = $this->input->get_request_header('User-agent');
		$hash = $this->input->get_request_header('X-gi-authorization');

		if( !defined('SIMPLETEST') ) {
			$this->_fetch_auth($ua, $hash);
		}
	}

	/**
	 * Forces authentication handler to reset
	 * @param  String $ua   User-agent
	 * @param  String $hash API key
	 */
	public function reload_auth($ua, $hash) {
		return $this->_fetch_auth($ua, $hash);
	}

	/**
	 * Get current authentication handler, user
	 * @return Object
	 */
	public function get_auth() {
		return $this->auth;
	}

	/**
	 * Get the account for the authenticated user
	 * @return Object
	 */
	public function get_account() {
		$acct = $this->db
			->where('id', $this->account_id)
			->get('accounts')
			->result();

		return $acct[0];
	}

	private function _fetch_auth($ua, $hash) {
		if( $this->CI->input->is_cli_request() ) {
			return;
		}

		if( $this->CI->uri->rsegment(1) == 'checkout' ) {
			return;
		}

		if( !$hash ) {
			$this->log->entry('No API Key', 4);
			$this->output->show_error('Invalid API Key');
			return;
		}

		$item = $this->db
			->where('single_access_token', $hash)
			->get('users')
			->result();

		if( !count($item) ) {
			$this->log->entry('Invalid API Key', 4);
			$this->output->show_error('Invalid API Key');
			return;
		}

		if( !$ua ) {
			$this->log->entry('User-Agent header is required', 4);
			$this->output->show_error('User-Agent header is required');
			return;
		}

		if( !$item[0]->active ) {
			$this->log->entry('User account is inactive (ID '.$item[0]->id.')', 4);
			$this->output->show_error('User account is inactive');
			return;
		}

		$this->auth = $item[0];
		$this->auth->ua = $ua;

	}

	public function __call($f, $args) {
		return call_user_func_array(array($this->CI, $f), $args);
	}

	public function __get($k) {
		if( $k == 'account' ) {
			$k = 'account_id';
		}
		if( $this->auth && property_exists($this->auth, $k)	) {
			return $this->auth->$k;
		}
		return $this->CI->$k;
	}

	public function __set($k, $v) {
		$this->CI->$k = $v;
	}

}