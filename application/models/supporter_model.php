<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Supporter model
 *
 * @class Supporter_model
 * @extends  GI_Model
 */
class Supporter_model extends GI_Model {

    public $id                  = false;
    public $account_id          = false;
    public $email_address       = false;
    public $first_name          = false;
    public $last_name           = false;
    public $postal_code         = false;
    public $street_address      = false;
    public $city                = false;
    public $state               = false;
    public $country             = false;
    public $supporter_token     = false;
    public $updated_at          = false;
    public $created_at          = false;
    public $total_donations     = false;
    public $donations_total     = false;

    public function __construct() {
        parent::__construct();

        // $this->load->model('Donation_model');
    }

    private function _get_donations_total() {
        $resp = $this->db
            ->select('sum(amount) as total')
            ->where('supporter_id', $this->id)
            ->where('account_id', $this->account_id)
            ->where('refunded', 0)
            ->from('donations')
            ->get()
            ->result();

        return $resp[0]->total;
    }

    private function _get_total_donations() {
        $resp = $this->db
            ->select('count(id) as total')
            ->where('supporter_id', $this->id)
            ->where('account_id', $this->account_id)
            ->where('refunded', 0)
            ->from('donations')
            ->get()
            ->result();

        return $resp[0]->total;
    }

    public function __donations() {
        return $this->Donation_model
            ->where('account_id', $this->account_id)
            ->where('supporter_id', $this->id)
            ->get()
            ->result();
    }

    /**
     * Overrides parent class save_entry method by automatically generating
     *  * updated_at
     *  * created_at
     *
     * Also, properly handles processing and storage of attached files
     *
     */
    public function save_entry() {

        if( !$this->id ) {
            $this->supporter_token = api_generate_token();
        }

        $this->updated_at = date('Y-m-d H:i:s');

        if( !$this->created_at ) {
            $this->created_at = date('Y-m-d H:i:s');
        }

        $this->total_donations = $this->_get_total_donations();
        $this->donations_total = $this->_get_donations_total();

        parent::save_entry();

        return $this;
    }

}