<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Transaction model
 *
 * @class Transaction_model
 * @extends  GI_Model
 */
class Transaction_model extends GI_Model {

    public $id                  = false;
    public $donation_id         = false;
    public $created_at          = false;
    public $updated_at          = false;
    public $stripe_id           = false;
    public $type                = false;
    public $total               = false;
    public $refunded            = false;

	public function __construct() {
		parent::__construct();

	}

    public static function for_donation($donation) {
        $ci = get_instance();

        $ci->load->model('Donation_model');

        $d = $ci->Donation_model
            ->where('donation_token', $donation->id_token)
            ->find();

        $m = new Transaction_model;
        return $m->where('donation_id', $d[0]->id)
            ->order_by('created_at asc')
            ->find();
    }

    /**
     * Overrides parent class save_entry method by automatically generating
     *  * permalink
     *  * updated_at
     *  * created_at
     *
     */
	public function save_entry() {

		$this->updated_at = date('Y-m-d H:i:s');

		if( !$this->created_at ) {
			$this->created_at = date('Y-m-d H:i:s');
		}

		parent::save_entry();

		return $this;
	}


}