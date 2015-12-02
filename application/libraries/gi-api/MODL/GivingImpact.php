<?php
/**
 * Giving Impact PHP Library
 *
 * Simple, fluent interface to the Giving Impact API
 *
 * 	<pre>
 *  	$API = new \MODL\GivingImpact(
 *          'My-User-Agent', 'MY-GI-API-KEY'
 *      );
 *
 *      $campaign = $API
 *      	->campaign
 *      	->fetch('XXXXX');
 *
 *      $stats = $campaign
 *      	->stats
 *      	->limit($max+1)
 *      	->offset($offset)
 *      	->fetch();
 * 	</pre>
 *
 * @package        	GivingImpact
 * @subpackage    	Libraries
 * @category    	Libraries
 * @author        	Minds on Design Lab, Inc.
 * @created			10/01/2012
 * @license         #
 */

namespace MODL;
use MODL\GivingImpact\Exception as GIException;
use MODL\GivingImpact\RestClient as RestClient;
use MODL\GivingImpact\Model\Campaign as Campaign;
use MODL\GivingImpact\Model\Opportunity as Opportunity;
use MODL\GivingImpact\Model\Donation as Donation;
use MODL\GivingImpact\Model\Stats as Stats;
use MODL\GivingImpact\Model\Supporter as Supporter;

require_once dirname(__FILE__).'/GivingImpact/Exception.php';
require_once dirname(__FILE__).'/GivingImpact/RestClient.php';
require_once dirname(__FILE__).'/GivingImpact/Model.php';

require_once dirname(__FILE__).'/GivingImpact/Model/Campaign.php';
require_once dirname(__FILE__).'/GivingImpact/Model/Opportunity.php';
require_once dirname(__FILE__).'/GivingImpact/Model/Donation.php';
require_once dirname(__FILE__).'/GivingImpact/Model/Stats.php';
require_once dirname(__FILE__).'/GivingImpact/Model/Supporter.php';

/**
 * GivingImpact library base class, also provides dependency injector
 *
 * @class GivingImpact
 * @namespace  \MODL\GivingImpact
 */
class GivingImpact {

	/**
	 * Base components for dependency injector
	 * @var array
	 */
	protected $components = array(
		'end_point'		=> '/api',
		'user_agent'	=> 'Internal-GI-UA',
		'api_key'		=> false,
	);

	/**
	 * Constructor
	 * @param String $user_agent
	 * @param String $api_key
	 */
	public function __construct($user_agent, $api_key, $end_point = false) {

		$this->user_agent = $user_agent;
		$this->api_key = $api_key;

		if( $end_point ) {
			$this->end_point = $end_point;
		}

		if( !function_exists('curl_init') ) {
			throw new GIException('CURL extension is required');
			return;
		}

		$v = curl_version();
		if( !($v['features'] & CURL_VERSION_SSL) ) {
			throw new GIException('SSL is required');
			return;
		}

		$this->initialize();
	}

	/**
	 * Initialize the dependency injector. Loads the REST client
	 * and models with base DI object.
	 *
	 */
	public function initialize() {

		// build and return new rest client with proper settings
		$this->restClient = function($_) {
			$rc = new RestClient($_);
			$rc->headers = array(
				'X-GI-Authorization: '.$_->api_key,
				'Content-Type: application/json'
			);

			$rc->url = $_->end_point;
			$rc->user_agent = $_->user_agent;

			return $rc;
		};

		$this->campaign = function($_) {
			return new Campaign($_);
		};
		$this->opportunity = function($_) {
			return new Opportunity($_);
		};
		$this->donation = function($_) {
			return new Donation($_);
		};
		$this->stats = function($_) {
			return new Stats($_);
		};
		$this->supporter = function($_) {
			return new Supporter($_);
		};

	}

	public function __get($k) {
		if( is_callable($this->components[$k]) ) {
			return $this->components[$k]($this);
		}

		return $this->components[$k];
	}

	public function __set($k, $v) {
		$this->components[$k] = $v;
	}

}
