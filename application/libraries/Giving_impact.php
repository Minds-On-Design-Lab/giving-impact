<?php
/**
 * Giving Impact CodeIgniter Wrapper
 *
 * You must set 'gi-api-key' in your main config.php with your
 * GI API key. You may also set 'gi-api-location' with the
 * GivingImpact API endpoint - otherwise the default endpoint
 * will be used.
 *
 *  <pre>
 *      $this->load->library('giving_impact');
 *
 *      $campaign = $this->giving_impact
 *          ->campaign
 *          ->fetch($this->uri->segment(2));
 *  </pre>
 *
 * @class Giving_Impact
 * @copyright  2013 Minds On Design Lab, Inc
 * @author  Mike Joseph <mikej@mod-lab.com>
 * @license MIT
 */
require_once dirname(__FILE__).'/gi-api/MODL/GivingImpact.php';

class Giving_Impact {

	private $CI = false;

	private $gi_client = false;

    /**
     * Constructor
     * @param array $c Configuration array
     */
	public function __construct($c) {
        $this->CI =& get_instance();
        $key = $this->CI->config->item('gi-api-key');

        $this->gi_client = new \MODL\GivingImpact(
            $c['gi-user-agent'], $key
        );

        if( array_key_exists('gi-api-location', $c) && $c['gi-api-location'] ) {
            $this->gi_client->end_point = $c['gi-api-location'];
        }

	}

	public function __get($k) {
	    return $this->gi_client->$k;
	}

	public function __call($f, $a) {
	    return call_user_func_array(array($this->gi_client, $f), $a);
	}

}