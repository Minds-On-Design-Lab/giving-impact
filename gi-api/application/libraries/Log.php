<?php

/**
 * Simple logging library
 *
 * @class Log
 */
class Log {

	private $CI 	= false;
	private $config = array();


	const INFO 		= 0;
	const DEBUG 	= 1;
	const NOTICE	= 2;
	const WARNING 	= 3;
	const ERROR 	= 4;
	const FATAL		= 5;

	public function __construct() {
		$this->CI = get_instance();

		$this->CI->config->load('log', true);

//		$this->CI->load->library(array('email', 'postmail'));
	}

	/**
	 * Add log entry
	 * @param  String  $message
	 * @param  integer $type    Can be INFO, DEBUG, NOTICE, WARNING, ERROR or FATAL
	 */
	public function entry($message, $type = 2) {

		$auth = new stdClass;
		$auth->id = 0;
		$auth->account = 0;
		$auth->ua = '/';

		if( property_exists($this->CI, 'authorization') ) {
			$auth = $this->CI->authorization->get_auth();
		}

		if( !property_exists($auth, 'account') ) {
			$auth->account = $auth->account_id;
		}

		$log = array(
			'ip' => $_SERVER['REMOTE_ADDR'],
			'uri' => $this->CI->uri->uri_string(),
			'message' => $message,
			'type' => $type,
			'type_string' => $this->_type_string($type),
			'trace' => false,
			'created' => time(),
			'user_agent' => $auth->ua,
			'api_key' => $auth->id,
			'account' => $auth->account,
			'version' => $this->CI->uri->api_version()
		);

		if( $type >= $this->CI->config->item('min_trace', 'log') ||
			$type == self::DEBUG ) {
			$log['trace'] = $this->_generate_trace();
		}

		$this->_write_db($log);

		if( $type >= $this->CI->config->item('min_warn', 'log') ) {
//			$this->_notify($log);
		}
	}

	private function _notify($log) {
		$email = $this->CI->config->item('warn_email', 'log');
		$from = $this->CI->config->item('warn_email_from', 'log');

		$this->CI->postmail->initialize();

		$this->CI->postmail->from(
			$from['address'], $from['name']
		);

		$this->CI->postmail->to($email);
		$this->CI->postmail->subject(sprintf(
			'%s from %s',
			$this->CI->config->item('warn_subject', 'log'),
			$log['host']
		));

		$message = <<<END
Log notification from %s

Type: %s
Time: %s
Path: %s
IP: %s

%s

Trace:

%s
END;
		$message = sprintf(
			$message, $log['host'], $log['type_string'],
			date($this->CI->config->item('date_format', 'log'), $log['created']),
			$log['uri'], $log['ip'], $log['message'], $log['trace']
		);


		$this->CI->postmail->message($message);
//		$this->CI->postmail->send();
	}


	private function _write_db($log) {
		$this->CI->db->insert('api_logs', $log);
	}

	private function _type_string($type) {
		$type_str = '';
		switch($type) {
			case 0:
				$type_str = 'INFO';
				break;
			case 1:
				$type_str = 'DEBUG';
				break;
			case 2:
				$type_str = 'NOTICE';
				break;
			case 3:
				$type_str = 'WARNING';
				break;
			case 4:
				$type_str = 'ERROR';
				break;
			case 5:
				$type_str = 'FATAL';
				break;
		}

		return $type_str;
	}

	private function _generate_trace() {
		$lines = array();

		foreach( debug_backtrace() as $item ) {
			if( array_key_exists('class', $item) && $item['class'] ) {
				$lines[] = sprintf(
					'%s:%s %s::%s',
					$item['file'], $item['line'],
					$item['class'], $item['function']
				);
			} else {
				$lines[] = sprintf(
					'%s:%s %s',
					$item['file'], $item['line'], $item['function']
				);
			}
		}

		return implode("\n", $lines);
	}
}