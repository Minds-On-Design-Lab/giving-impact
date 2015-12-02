<?php

/**
 * Override and extend core Output class
 *
 * @class GI_Output
 * @extends  CI_Output
 */
class GI_Output extends CI_Output {

	/**
	 * Overrides base display handler. If in test mode, always
	 * returns display string. Otherwise attempts to determine output
	 * type and add 'Content-Type' header to result
	 * @param  string $output
	 * @return String
	 */
	public function _display($output = '') {
		if( defined('SIMPLETEST') ) {
			return parent::_display($output);
		}

		$fmt = get_instance()->uri->output_format();

		if( !$fmt ) {
			return parent::_display($output);
		}

		if( !headers_sent() ) {
			switch($fmt) {
				case 'json':
					header('Content-Type: application/json; charset=UTF-8');
					break;

				case 'xml':
				default:
					header('Content-Type: application/xml; charset=UTF-8');
					break;
			}
		}

		if( $fmt == 'json' && get_instance()->input->get('callback') ) {
			$cb = get_instance()->input->get('callback');
			$output = sprintf('%s(%s)', $cb, $this->final_output);
		}
		return parent::_display($output);
	}

	/**
	 * Handles the proper display of errors and error codes to the browser
	 *
	 * Type can be:
	 *
	 *  * 400 - Bad request
	 *  * 401 - Unauthorized
	 *  * 402 - Request failed
	 *  * 404 - Not found
	 *  * 500 - Unprocessable
	 *
	 * @param  String $message
	 * @param  Int $type
	 */
	public function show_error($message, $type = '402', $status = "invalid_request") {
		if( defined('SIMPLETEST') ) {
			return get_instance()->load->view('error', array('message' => $message, 'type' => $status));
		}

		$fmt = get_instance()->uri->output_format();
		if( !headers_sent() ) {
			switch($fmt) {
				case 'json':
					header('Content-Type: application/json; charset=UTF-8');
					break;

				case 'xml':
					header('Content-Type: application/xml; charset=UTF-8');
					break;
			}

			switch($type) {
				case '400':
					header('HTTP/1.1 400 Bad Request');
					break;
				case '401':
					header('HTTP/1.1 401 Unauthorized');
					break;
				case '402':
					header('HTTP/1.1 402 Request Failed');
					break;
				case '404':
					header('HTTP/1.1 404 Not Found');
					break;

			}
		}
		echo get_instance()->load->view('error', array('message' => $message, 'type' => $status), true);
		exit;
	}

}