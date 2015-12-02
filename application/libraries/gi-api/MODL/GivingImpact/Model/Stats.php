<?php

namespace MODL\GivingImpact\Model;
use MODL\GivingImpact\Exception as GIException;

/**
 * Stats model
 *
 * @class Stats
 * @extends  \MODL\GivingImpact\Model
 * @namespace  \MODL\GivingImpact\Model
 */
class Stats extends \MODL\GivingImpact\Model {

    public $date = false;
    public $total_donations = false;
    public $donation_total = false;
    public $shares_fb = false;
    public $shares_twitter = false;

    private $stack = array();

    public function __construct($c, $data = false) {
        $this->container = $c;

        if( $data ) {
            $this->assign($data);
        }
    }

    /**
     * Fetch stats
     *
     * @return Array
     */
    public function fetch($token = null) {
        $rc = $this->container->restClient;

        if( $this->campaign_token ) {
            $rc->url = sprintf(
                '%s/v2/campaigns/%s/stats/log',
                $rc->url,
                $this->campaign_token
            );
        } else {
            $rc->url = sprintf(
                '%s/v2/opportunities/%s/stats/log',
                $rc->url,
                $this->opportunity_token
            );

        }

        $data = $rc->get($this->properties);
        $out = array();

        foreach( $data->stats as $d ) {
            $out[] = new $this($this->container, $d);
        }

        return $out;
    }

    /**
     * Set parent campaign
     * @param  String $token
     * @return Object
     */
    public function campaign($token) {
        $this->campaign_token = $token;

        return $this;
    }

    /**
     * Set parent opportunity
     * @param  String $token
     * @return Object
     */
    public function opportunity($token) {
        $this->opportunity_token = $token;

        return $this;
    }

    public function __get($k) {
        $f = sprintf('__%s', $k);
        if( is_callable(array($this, $f)) ) {
            return call_user_func(array($this, $f));
        }
    }
}
