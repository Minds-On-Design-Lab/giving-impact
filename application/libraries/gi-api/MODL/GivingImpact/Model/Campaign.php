<?php

namespace MODL\GivingImpact\Model;
use MODL\GivingImpact\Exception as GIException;

/**
 * Campaign model
 *
 * Campaign model provides a fluent interface
 *
 *  <pre>
 *      $campaigns = $API
 *          ->campaign
 *          ->status('active')
 *          ->limit(10)
 *          ->fetch();
 *  </pre>
 *
 * @class Campaign
 * @extends  \MODL\GivingImpact\Model
 * @namespace  \MODL\GivingImpact\Model
 */
class Campaign extends \MODL\GivingImpact\Model {

    public $id_token = false;
    public $title = false;
    public $description = false;
    public $youtube_id = false;
    public $donation_total = false;
    public $donation_target = false;
    public $donation_url = false;
    public $share_url = false;
    public $hash_tag = false;
    public $status = false;
    public $has_giving_opportunities = false;
    public $display_donation_target = false;
    public $display_donation_total = false;
    public $enable_donation_levels = false;
    public $donation_levels = false;
    public $image_url = false;
    public $custom_fields = false;
    public $campaign_fields = false;
    public $widget = false;
    public $header_font_color = false;
    public $campaign_color = false;
    public $donation_minimum = false;
    public $frequency_type = 0;
    public $frequency_period = 0;
    // public $send_receipt = false;
    // public $email_org_name = false;
    // public $reply_to_address = false;
    // public $bcc_address = false;
    // public $street_address = false;
    // public $street_address_2 = false;
    // public $city = false;
    // public $state = false;
    // public $postal_code = false;
    // public $country = false;
    // public $receipt_body = false;
    public $analytics_id = false;
    public $receipt = false;

    public $image_type = false;
    public $image_file = false;

	protected $path = 'v2/campaigns';

    private $stack = array();

	public function __construct($c, $data = false) {
		$this->container = $c;

		if( $data ) {
		    $this->assign($data);
		}
	}

    /**
     * Create new campaign
     *  <pre>
     *      $model->create(array('title' => 'Testing'));
     *  </pre>
     *
     * @throws MODL\GivingImpact\Exception If $data is not array
     * @param  Array $data
     * @return Object
     */
    public function create($data) {

        if( !is_array($data) ) {
            throw new GIException('Expected array');
            return;
        }

        $rc = $this->container->restClient;
        $rc->url = sprintf(
            '%s/%s', $rc->url, $this->path
        );

        $return = $rc->post($data);

        return new $this($this->container, $return->campaign);
    }

    /**
     * Save existing campaign
     *
     *  <pre>
     *      $campaign = $campaign->fetch('XXXX');
     *      $campaign->title = 'Foo';
     *      $campaign->save();
     *  </pre>
     *
     * @throws MODL\GivingImpact\Exception If campaign has no ID_TOKEN
     * @return Object this
     */
    public function save() {
        if( !$this->id_token ) {
            throw new GIException('Please use create method');
            return;
        }

        $data = array();
        foreach( $this->publicProperties() as $prop ) {
            $data[$prop] = $this->$prop;
        }

        $rc = $this->container->restClient;
        $rc->url = sprintf(
            '%s/%s/%s', $rc->url, $this->path, $this->id_token
        );

        $return = $rc->post($data);

        return new $this($this->container, $return->campaign);
    }

    /**
     * Fetch campaigns via api
     * @param  boolean $token OPTIONAL
     * @return Array
     */
	public function fetch($token = false) {
		if( $token ) {
			$data = parent::fetch($token);
            return new $this($this->container, $data->campaign);
		}

		$rc = $this->container->restClient;
		$rc->url = $rc->url.'/'.$this->path;

		$data = $rc->get($this->properties);
		$out = array();
		foreach( $data->campaigns as $c ) {
		    $out[] = new $this($this->container, $c);
		}

		return $out;
	}

    public function __get($k) {
        $f = sprintf('__%s', $k);
        if( is_callable(array($this, $f)) ) {
            return call_user_func(array($this, $f));
        }
    }

    /**
     * Opportunities computed property. Automatically fetch, format and return
     * child opportunities from API connection
     * @return Array
     */
    public function __opportunities() {
        if( !$this->id_token ) {
            return false;
        }

        if( array_key_exists('opps', $this->stack) ) {
            return $this->stack['opps'];
        }

        $opps = $this->container->opportunity
            ->campaign($this->id_token);

        $this->stack['opps'] = $opps;

        return $opps;
    }

    /**
     * Donations computed property. Automatically fetches and processes donations
     * from the API
     * @return Array
     */
    public function __donations() {
        if( !$this->id_token ) {
            return false;
        }

        if( array_key_exists('donations', $this->stack) ) {
            return $this->stack['donations'];
        }

        $donations = $this->container->donation
            ->campaign($this->id_token);

        $this->stack['donations'] = $donations;

        return $donations;
    }

    public function __donation() {
        return $this->container->donation
            ->campaign($this->id_token);
    }

    /**
     * Stats computed property. Automatically fetches and processes stats
     * @return Array
     */
    public function __stats() {
        if( !$this->id_token ) {
            return false;
        }
        if( array_key_exists('stats', $this->stack ) ) {
            return $this->stack['stats'];
        }

        $stats = $this->container->stats
            ->campaign($this->id_token);

        $this->stack['stats'] = $stats;

        return $stats;
    }

}
