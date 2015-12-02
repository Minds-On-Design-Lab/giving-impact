<?php

namespace MODL\GivingImpact\Model;
use MODL\GivingImpact\Exception as GIException;

/**
 * Opportunity model
 *
 *  <pre>
 *      $campaign = $API
 *          ->campaign
 *          ->limit(1)
 *          ->fetch();
 *
 *      $opportunities = $campaign
 *          ->opportunities
 *          ->sort('title')
 *          ->status('both')
 *          ->fetch();
 *
 *  </pre>
 *
 * @class Opportunity
 * @extends  \MODL\GivingImpact\Model
 * @namespace  \MODL\GivingImpact\Model
 */
class Opportunity extends \MODL\GivingImpact\Model {

    public $id_token = false;
    public $campaign_token = false;
    public $title = false;
    public $description = false;
    public $youtube_id = false;
    public $donation_total = false;
    public $donation_target = false;
    public $donation_url = false;
    public $share_url = false;
    public $shares_fb = false;
    public $shares_twitter = false;
    public $hash_tag = false;
    public $status = false;
    public $enable_donation_levels = false;
    public $donation_levels = false;
    public $image_url = false;
    public $custom_fields = false;
    public $campaign_responses = false;
    public $widget = false;
    public $campaign = false;
    public $analytics_id = false;

    public $supporters = false;

    public $image_type = false;
    public $image_file = false;

	protected $path = 'v2/opportunities';

    private $stack = array();
    private $supporter_token;

	public function __construct($c, $data = false) {
		$this->container = $c;

		if( $data ) {
		    $this->assign($data);
		}
	}

    /**
     * Create new opportunity
     *
     * @throws MODL\GivingImpact\Exception If $data is not array
     * @param  Array $data
     * @return Object
     */
    public function create($data = false) {

        if( !$data ) {
            $data = array();
            foreach( $this->publicProperties() as $prop ) {
                if( $prop == 'opportunity_token' ) {
                    continue;
                }
                $data[$prop] = $this->$prop;
            }
        }

        if( !is_array($data) ) {
            throw new GIException('Expected array');
            return;
        }

        if( !array_key_exists('campaign_token', $data)
            || !$data['campaign_token'] ) {

            throw new GIException('No parent campaign found');
            return;
        }

        $rc = $this->container->restClient;
        $rc->url = sprintf(
            '%s/%s', $rc->url, $this->path
        );

        $return = $rc->post($data);

        return new $this($this->container, $return->opportunity);
    }

    /**
     * Save existing opportunity
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

        return new $this($this->container, $return->opportunity);
    }

    /**
     * Fetches opportunity data from API
     * @param  boolean $token
     * @return Array
     */
	public function fetch($token = false) {
		if( $token ) {
			$data = parent::fetch($token);
            return new $this($this->container, $data->opportunity);
		}

        if( $this->supporter_token ) {
            $rc = $this->container->restClient;
            $rc->url = sprintf(
                '%s/v2/supporters/%s/opportunities',
                $rc->url,
                $this->supporter_token
            );
        } else {
            if( !$this->campaign_token ) {
                throw new GIException('Parent campaign token required');
                return;
            }
            $rc = $this->container->restClient;
            $rc->url = sprintf(
                '%s/v2/campaigns/%s/opportunities',
                $rc->url,
                $this->campaign_token
            );
        }

		$data = $rc->get($this->properties);
		$out = array();

		foreach( $data->opportunities as $o ) {
		    $out[] = new $this($this->container, $o);
		}

		return $out;
	}

    /**
     * Set parent campaign
     * @param  String $token
     * @return Object        this
     */
	public function campaign($token) {
	    $this->campaign_token = $token;

	    return $this;
	}

    /**
     * Donations computed property
     * @return Array of donations
     */
    public function __donations() {
        if( !$this->id_token ) {
            return false;
        }

        if( array_key_exists('donations', $this->stack) ) {
            return $this->stack['donations'];
        }

        $donations = $this->container->donation
            ->opportunity($this->id_token);

        $this->stack['donations'] = $donations;

        return $donations;
    }

    /**
     * Stats computed property
     *
     * @return Array of stats
     */
    public function __stats() {
        if( !$this->id_token ) {
            return false;
        }
        if( array_key_exists('stats', $this->stack ) ) {
            return $this->stack['stats'];
        }

        $stats = $this->container->stats
            ->opportunity($this->id_token);

        $this->stack['stats'] = $stats;

        return $stats;
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
