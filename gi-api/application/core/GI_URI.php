<?php

/**
 * Extend core URI class
 *
 * @class GI_URL
 * @extends  CI_URI
 */
class GI_URI extends CI_URI {

	private $output_format = 'json';

	private $allowed_formats = array('json', 'xml');

	// override the fetch URI string to strip out format property
	public function _fetch_uri_string() {
		parent::_fetch_uri_string();
		$str = $this->uri_string();

		foreach( $this->allowed_formats as $fmt ) {
			$use_format = false;
			if( strpos($str, '.'.$fmt) === false ) {
				continue;
			}

			$use_format = $fmt;
			break;
		}

		if( !$use_format ) {
			return;
		}

		$str = str_replace('.'.$use_format, '', $str);
		$this->_set_uri_string($str);

		$this->output_format = $use_format;
	}

	/**
	 * Determine current output format (either json or xml)
	 * @return String
	 */
	public function output_format() {
		return $this->output_format;
	}

	/**
	 * Determine API version requested
	 * @return String
	 */
	public function api_version() {

		$seg = get_instance()->uri->segment(1);

		if( preg_match('/v(\d*)/', $seg, $matches) ) {
			if( is_int((int) $matches[1]) ) {
				return $matches[1];
			}
		}

		return DEFAULT_API_VERSION;

	}

}

if ( ! function_exists('is_https'))
{
	/**
	 * Is HTTPS?
	 *
	 * Determines if the application is accessed via an encrypted
	 * (HTTPS) connection.
	 *
	 * @return	bool
	 */
	function is_https()
	{
		if ( ! empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
		{
			return TRUE;
		}
		elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
		{
			return TRUE;
		}
		elseif ( ! empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off')
		{
			return TRUE;
		}
		return FALSE;
	}
}
