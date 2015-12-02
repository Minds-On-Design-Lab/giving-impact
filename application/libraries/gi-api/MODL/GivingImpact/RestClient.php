<?php

namespace MODL\GivingImpact;
use MODL\GivingImpact\Exception as GIException;

/**
 * REST API Client base. Uses cURL library to communicate with API
 *
 * 	<pre>
 *  	$client = new \MODL\GivingImpact\RestClient
 *  	$client->url = 'http://base.url';
 *
 * 		$return = $client->get(data('sort'=>'created|asc'));
 * 	</pre>
 *
 * @class RestClient
 * @namespace  \MODL\GivingImpact
 */
class RestClient {

	/**
	 * Base URL
	 * @var boolean
	 */
	public $url = false;

	/**
	 * Heaer stack
	 * @var array
	 */
	public $headers = array();

	/**
	 * Optional port
	 * @var boolean
	 */
	public $port = false;

	/**
	 * Base user agent string (default is "MODL Client")
	 * @var string
	 */
	public $user_agent = 'MODL Client';

	/**
	 * Dependency injection container
	 * @var Object
	 */
	private $container = false;

	/**
	 * Debug Mode
	 * @var boolean
	 */
	private $debug = false;

	/**
	 * Constructor
	 * @param Object $c Dependency injector
	 */
	public function __construct($c) {
		$this->container = $c;

	}

	/**
	 * Construct a fully qualified URL with path and optional key/value arguments
	 *
	 * 	<pre>
	 *  	echo $client->buildURL('donations/XXX', array('foo'=>'bar'));
	 *  	// base.url/donations/XXX?foo=bar
	 * 	</pre>
	 * @param  String $path URL path WITHOUT base URL
	 * @param  array  $args GET arguments
	 * @return String
	 */
	public function buildURL($path, $args = array()) {
		if( !$this->url ) {
			throw new GIException('URL is not defined');
			return;
		}

		$query = '';
		if( count($args) ) {
			$query = '?'.http_build_query($args);
		}
		return sprintf(
			'%s/%s%s', rtrim($this->url, '/'), rtrim($path, '/'), $query
		);
	}

	/**
	 * Perform a GET action
	 * @param  Array $data OPTIONAL data to send in GET
	 * @return Object        JSON decoded object
	 */
	public function get($data = false) {

		if( $data ) {
			$this->url = $this->buildURL('', $data);
		}

		$raw_json = $this->curlFetch();
		$data = json_decode($raw_json);

		if( !$data ) {
			throw new GIException('Unable to connect to Giving Impact API');
			return;
		}
		if( $data->error ) {
			throw new GIException($data->message);
			return;
		}

		return $data;

	}

	/**
	 * Perform a POST action
	 * @param  Array $data POST data
	 * @return Object       JSON decoded object
	 */
	public function post($data) {
		$raw_json = $this->curlFetch($data);

		$return = json_decode($raw_json);

        if( !is_object($return) ) {
            throw new GIException($raw_json);
            return;
        }

		if( $return->error ) {
			throw new GIException($return->message);
			return;
		}

		return $return;
	}

	/**
	 * Query REST resource, you should not have to call this method manually,
	 * as it is called by the get and post methods.
	 *
	 * @param  Mixed $data
	 * @return String
	 */
	public function curlFetch($data = false) {
		if( $data ) {
			$data = json_encode($data);
		}

		$url = $this->url;
		if( strpos($url, 'http') === false ) {
			$url = 'https://'.$url;
		}

        if (strpos($url, 'localhost')) {
            $url = str_replace('localhost', '127.0.0.1', $url);
        }

		$ch = curl_init();
		$verbose = $this->debug ? fopen('php://temp', 'rw+') : false;

		if( $verbose ) {
			curl_setopt($ch, CURLOPT_STDERR, $verbose);
		}

		// if( strpos($url, ':') !== false ) {
		// 	// we have a port;
		// 	preg_match('/(\:([0-9]*))/', $url, $matches);
		// 	$port = $matches[2];

		// 	$url = str_replace($matches[1], '', $url);
		// 	curl_setopt($ch, CURLOPT_PORT, $port);
		// }
		curl_setopt($ch, CURLOPT_VERBOSE, true);

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
    	curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__).'/cacert.pem');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);


		if( count($this->headers) ) {
			curl_setopt(
				$ch,
				CURLOPT_HTTPHEADER,
				$this->headers
			);
		}

		if( $data ) {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		}

		$data = curl_exec($ch);

		if( $verbose ) {
			if( $data === false ) {
				printf("cUrl error (#%d): %s<br>\n", curl_errno($ch),
				htmlspecialchars(curl_error($ch)));

				curl_close($ch);

				rewind($verbose);
				$verboseLog = stream_get_contents($verbose);

				echo "Verbose information:\n<pre>", htmlspecialchars($verboseLog), "</pre>\n";
				return false;
			}
		}

		curl_close($ch);

		return $data;
	}

}
