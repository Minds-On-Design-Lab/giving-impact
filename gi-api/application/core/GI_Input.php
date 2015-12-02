<?php
/**
 * Adds API parsing support to base Input class
 *
 * @class GI_Input
 * @extends CI_Input
 */
class GI_Input extends CI_Input {

	public $post_body = false;

	/**
	 * Extends and overrides parent constructor to check for
	 * POSTed JSON data and properly parse it. Reads input directy
	 * from stdin.
	 */
	public function __construct() {
		parent::__construct();


		if( array_key_exists('REQUEST_URI', $_SERVER) ) {
			$uri = $_SERVER['REQUEST_URI'];
			if( strpos($uri, '?') !== false ) {
				$uri = substr($uri, strpos($uri, '?')+1);

				parse_str($uri, $bits);

				if (function_exists('array_replace_recursive')) {
					$_GET = array_replace_recursive($_GET, $bits);
				} else {
					$_GET = array_merge_recursive($_GET, $bits);
				}
			}

			if( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
				// handles POSTed input without the form encoding header
				$handle = fopen('php://input','r');
				$data = '';

				while( ($line = fgets($handle)) ) {
					$data .= $line;
				}

				if( !$data ) {
					return;
				}

				$this->_parse($data);
			}
		}
	}

	/**
	 * Parses inbound data. If 'Content-Type' is set to 'application/json'
	 * processes data as JSON, otherwise expects standard query parameters.
	 *
	 * @param  String $data
	 */
	public function _parse($data) {

		if( array_key_exists('CONTENT_TYPE', $_SERVER) &&
			strpos($_SERVER["CONTENT_TYPE"], 'application/json') !== false
		) {
			$this->post_body = json_decode($data);
		} else {

			$data = explode('&', $data);
			$post = array();
			foreach( $data as $item ) {
				$temp = explode('=', $item);
				$post[$temp[0]] = html_entity_decode($temp[1]);
			}

			$_POST = $post;
		}
	}

	/**
	 * Filter and return the 'status' query parameter
	 *
	 * @return Int
	 */
	public function status() {
		if( $this->get('status') ) {
			switch(	$this->get('status') ) {
				case 'active':
					return 1;
					break;
				case 'inactive':
					return 0;
					break;
				case 'both':
					return false;
					break;
				default:
					return 1;
					break;
			}
		}

		return 1;
	}

	/**
	 * Filter and return limit query parameter.
	 * If no limit found, returns 'false'
	 * @return Int
	 */
	public function limit() {
		if( $this->get('limit') ) {
			return (int) $this->get('limit');
		}

		return false;
	}

	/**
	 * Filter and return offset query parameter.
	 * If no offset found, returns 'false'
	 * @return Int
	 */
	public function offset() {
		if( $this->get('offset') ) {
			return (int) $this->get('offset');
		}

		return false;
	}

	/**
	 * Filter and return the sort property query parameter.
	 * This is the property before the vertical pipe ('|'). Returns
	 * 'false' if no sort property found.
	 * @return String
	 */
	public function sort_name($type = 'donations') {
		$s = urldecode($this->get('sort'));
		$s = str_replace('%7C', '|', $s);
		if( !$s ) {
			return false;
		}
		if( strpos($s, '|') === false ) {
			$val = $s;
		} else {
			$s = explode('|', $s);
			$val = $s[0];
		}

		// some manual transforms
		switch($val) {
			case 'donation_total':
				if( $type == 'campaigns' ) {
					$val = 'current';
				} else {
					$val = 'amount';
				}
				break;
			case 'donation_target':
				$val = 'target';
				break;
			case 'donation_minimum':
				$val = 'minimum_donation_amount';
				break;
		}

		return $val;
	}

	/**
	 * Filter and return the sort direction. This is the property
	 * after the vertical pipe ('|'). Returns 'false' of no
	 * sort directives are found
	 * @return String
	 */
	public function sort_dir() {
		$s = urldecode($this->get('sort'));
		$s = str_replace('%7C', '|', $s);

		if( !$s ) {
			return false;
		}
		if( strpos($s, '|') === false ) {
			return false;
		}

		$s = explode('|', $s);
		if( $s[1] == 'asc' || $s[1] == 'desc' ) {
			return $s[1];
		}

		return false;
	}

	/**
	 * Filter and return 'related' query parameter. Returns 'false' if
	 * no related query parameter found
	 * @return String
	 */
	public function related() {
		$s = $this->get('related');

		if( !$s ) {
			return false;
		}

		return $s;
	}

}