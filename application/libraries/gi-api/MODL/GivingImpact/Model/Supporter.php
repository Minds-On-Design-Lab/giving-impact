<?php

namespace MODL\GivingImpact\Model;
use MODL\GivingImpact\Exception as GIException;

/**
 * Supporter model
 *
 * This model implements a fluent interface
 *
 *  <pre>
 *      $supporters = $API
 *          ->supporter
 *          ->limit(10)
 *          ->related(true)
 *          ->fetch();
 *  </pre>
 *
 * @class Supporter
 * @extends  \MODL\GivingImpact\Model
 * @namespace \Model\GivingImpact\Model
 */
class Supporter extends \MODL\GivingImpact\Model {

    public $first_name = false;
    public $last_name = false;
    public $email_address = false;
    public $street_address = false;
    public $city = false;
    public $state = false;
    public $postal_code = false;
    public $country = false;
    public $donations_total = false;
    public $total_donations = false;

	protected $path = 'v2/supporters';

    private $stack = array();

    private $use_donation = false;

	public function __construct($c, $data = false) {
		$this->container = $c;

		if( $data ) {
		    $this->assign($data);
		}
	}

    /**
     * Fetch subscriber
     * @param  boolean $token OPTIONAL
     * @return Array OR Object
     */
	public function fetch($token = false) {
		if( $token ) {
            $rc = $this->container->restClient;
            $rc->url = $rc->url.'/v2/supporters/'.$token;

            $data = $rc->get($this->properties);
            return new $this($this->container, $data->supporter);
		}

		$rc = $this->container->restClient;

        $rc->url = sprintf(
            '%s/v2/supporters',
            $rc->url
        );

        if( strpos($this->properties['sort'], 'created_at') !== false ) {
            $this->properties['sort'] = false;
        }

		$data = $rc->get($this->properties);
		$out = array();

		foreach( $data->supporters as $d ) {
		    $out[] = new $this($this->container, $d);
		}

		return $out;
	}

    /**
     * Save existing supporter
     *
     * @throws MODL\GivingImpact\Exception If ID_TOKEN is not set
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
            '%s/%s/%s', $rc->url, $this->path, $this->id_token
        );

        $return = $rc->post($data);

        return new $this($this->container, $return->supporter);
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
            ->supporter($this->id_token);

        $this->stack['donations'] = $donations;

        return $donations;
    }

    public function __opportunities() {
        if( !$this->id_token ) {
            return false;
        }

        if( array_key_exists('opportunities', $this->stack) ) {
            return $this->stack['opportunities'];
        }

        $opportunities = $this->container->opportunity
            ->supporter($this->id_token);

        $this->stack['opportunities'] = $opportunities;

        return $opportunities;
    }

    public function __get($k) {
        $f = sprintf('__%s', $k);
        if( is_callable(array($this, $f)) ) {
            return call_user_func(array($this, $f));
        }
    }
}
