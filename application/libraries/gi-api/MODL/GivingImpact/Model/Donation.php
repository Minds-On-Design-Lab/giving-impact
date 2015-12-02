<?php

namespace MODL\GivingImpact\Model;
use MODL\GivingImpact\Exception as GIException;

/**
 * Donation model
 *
 * This model implements a fluent interface
 *
 *  <pre>
 *      $donations = $API
 *          ->donation
 *          ->limit(10)
 *          ->related(true)
 *          ->sort('created_at|desc')
 *          ->fetch();
 *  </pre>
 *
 * @class Donation
 * @extends  \MODL\GivingImpact\Model
 * @namespace \Model\GivingImpact\Model
 */
class Donation extends \MODL\GivingImpact\Model {

    public $first_name = false;
    public $last_name = false;
    public $billing_address1 = false;
    public $billing_city = false;
    public $billing_state = false;
    public $billing_postal_code = false;
    public $billing_country = false;
    public $donation_total = false;
    public $donation_level_id = false;
    public $contact = false;
    public $email_address = false;
    public $referrer = false;
    public $offline = false;
    public $created_at = false;
    public $campaign = false;
    public $opportunity = false;
    public $custom_responses = false;
    public $donation_date = false;
    public $card = false;
    public $refunded = false;

	protected $path = false;

    private $stack = array();

    private $campaign_token;
    private $opportunity_token;
    private $supporter_token;

	public function __construct($c, $data = false) {
		$this->container = $c;

		if( $data ) {
		    $this->assign($data);
		}
	}

    /**
     * Fetch donation based
     * @param  boolean $token OPTIONAL
     * @return Array OR Object
     */
	public function fetch($token = false) {
		if( $token ) {
            $rc = $this->container->restClient;
            $rc->url = $rc->url.'/v2/donations/'.$token;

            $data = $rc->get($this->properties);
            return new $this($this->container, $data->donation);
		}

		$rc = $this->container->restClient;

        if( !$this->campaign_token && !$this->opportunity_token && !$this->supporter_token ) {
            $rc->url = sprintf(
                '%s/v2/donations',
                $rc->url
            );
        } elseif( $this->campaign_token ) {
            $rc->url = sprintf(
                '%s/v2/campaigns/%s/donations',
                $rc->url,
                $this->campaign_token
            );
        } elseif( $this->supporter_token ) {
            $rc->url = sprintf(
                '%s/v2/supporters/%s/donations',
                $rc->url,
                $this->supporter_token
            );
        } else {
            $rc->url = sprintf(
                '%s/v2/opportunities/%s/donations',
                $rc->url,
                $this->opportunity_token
            );

        }

		$data = $rc->get($this->properties);
		$out = array();

		foreach( $data->donations as $d ) {
		    $out[] = new $this($this->container, $d);
		}

		return $out;
	}

    /**
     * Create new offline donation
     *
     * @throws MODL\GivingImpact\Exception If $data is not array
     * @param  Array $data
     * @return Object
     */
    public function create($data = false) {

        if( !$data ) {
            $data = array();
            foreach( $this->publicProperties() as $prop ) {
                if( $prop == 'campaign_token' || $prop == 'opporunity_token' ) {
                    continue;
                }
                $data[$prop] = $this->$prop;
            }
        }

        $data['contact'] = $this->contact ? '1' : '0';

        if( $this->campaign_token ) {
            $data['campaign'] = $this->campaign_token;
        } elseif( $this->opporunity_token ) {
            $data['opporunity'] = $this->opporunity_token;
        }

        $data['contact'] = $data['contact'] ? '1' : '0';

        if( !is_array($data) ) {
            throw new GIException('Expected array');
            return;
        }

        if( !array_key_exists('campaign', $data) && !array_key_exists('opportunity', $data) ) {
            throw new GIException('No parent campaign found');
            return;
        }

        $rc = $this->container->restClient;
        $rc->url = sprintf(
            '%s/v2/donations',
            $rc->url
        );

        $return = $rc->post($data);

        return new $this($this->container, $return->donation);
    }

    /**
     * Save edited donation
     *
     * @throws MODL\GivingImpact\Exception If no ID_TOKEN is set
     * @return Object
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
            '%s/v2/donations/%s', $rc->url, $this->id_token
        );

        $return = $rc->post($data);

        return new $this($this->container, $return->donation);
    }

    /**
     * Set parent campaign
     * @param  String $token
     * @return Object this
     */
	public function campaign($token) {
	    $this->campaign_token = $token;

	    return $this;
	}

    /**
     * Set parent opportunity
     * @param  String $token
     * @return Object        this
     */
	public function opportunity($token) {
	    $this->opportunity_token = $token;

	    return $this;
	}

    /**
     * Set parent supporter
     * @param  String $token
     * @return Object        this
     */
    public function supporter($token) {
        $this->supporter_token = $token;

        return $this;
    }

    public function __get($k) {
        $f = sprintf('__%s', $k);
        if( is_callable(array($this, $f)) ) {
            return call_user_func(array($this, $f));
        }
    }
}
