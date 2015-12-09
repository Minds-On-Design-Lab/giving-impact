<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Plan model
 *
 * @class Plan_model
 * @extends  GI_Model
 */
class Plan_model extends GI_Model {

    public $id                  = false;
    public $account_id          = false;
    public $created_at          = false;
    public $updated_at          = false;
    public $stripe_plan_id      = false;
    public $stripe_subscription_id= false;
    public $currency            = false;
    public $donation_total      = false;
    public $frequency_type      = false;
    public $frequency_period    = false;
    public $donation_id         = false;
    public $plan_token          = false;

    public function __construct() {
        parent::__construct();

        $this->load->helper('api');

    }

    /**
     * Overrides parent class save_entry method by automatically generating
     *  * plan_token
     *  * updated_at
     *  * created_at
     *
     * Also, properly handles processing and storage of attached files
     *
     */
	public function save_entry() {

        $this->plan_token = api_generate_token();

		$this->updated_at = date('Y-m-d H:i:s');

		if( !$this->created_at ) {
			$this->created_at = date('Y-m-d H:i:s');
		}

		parent::save_entry();

		return $this;
	}

}