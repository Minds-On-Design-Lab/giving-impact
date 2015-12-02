<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Donation model
 *
 * @class Donation_model
 * @extends  GI_Model
 */
class Donation_model extends GI_Model {

	public $id = false;
	public $first_name = false;
	public $last_name = false;
	public $billing_address1 = false;
	public $billing_city = false;
	public $billing_state = false;
	public $billing_postal_code = false;
	public $billing_country = false;
	public $amount = false; //donation_total
	public $email_address = false; //email
	public $referrer = false;
	// public $description = false;
	public $donation_level_id = null;
	public $contact = false;
	public $donation_date = false; // created_at
	public $offline = false;
	public $campaign_id = false;
	public $campaign_group_id = false;
	public $updated_at = false;
	public $created_at = false;
	public $account_id = false;
	public $donation_token = false;
	public $complete = false;
	public $stripe_charge_id = null;
	public $refunded = 0;

	public function __construct() {
		parent::__construct();

		$this->load->model('Campaign_model');
		$this->load->model('Opportunity_model');
		$this->load->model('Supporter_model');

		$this->load->helper('api');
	}

	/**
	 * Select complete fetches extra necessary fields that cannot be set
	 * by model and bulds correct join syntax
	 *
	 * @return Object this
	 */
	public function select_complete() {
		$this->select(
			'donations.'.implode(
				', donations.', $this->get_public_properties()
			)
			.', campaigns.campaign_token'
		)
		->join('campaigns', 'donations.campaign_id = campaigns.id', 'left')
		->group_by('donations.id');

		return $this;
	}

	/**
	 * Overrides parent save_entry method, sets
	 *
	 *  * donation_token
	 *  * created_at
	 *  * updated_at
	 *  * donation_level_description if avaliable
	 *
	 * @return Object this
	 */
	public function save_entry() {
		if( !$this->id ) {
			$this->donation_token = api_generate_token();
		}

		$this->updated_at = date('Y-m-d H:i:s');

		if( !$this->created_at ) {
			$this->created_at = date('Y-m-d H:i:s');
		}

		if ($this->donation_level_id) {
			$this->db->select('description')
				->where('id', $this->donation_level_id)
				->from('campaign_levels');
			$query = $this->db->get();
			$row = $query->row();
			$this->description = $row->description;
		}

		parent::save_entry();

		return $this;
	}

	public function __supporter() {
		if( $this->supporter_id ) {
			$supporter = $this->Supporter_model
				->where('account_id', $this->account_id)
				->where('id', $this->supporter_id)
				->find();
			if( $supporter && count($supporter) ) {
				return $supporter[0];
			}
		}

		return false;
	}

	/**
	 * Campaign computed property. Returns parent campaign
	 * @return Object
	 */
	public function __campaign() {
		if( $this->campaign_id == $this->campaign_group_id ) {
			return $this->Campaign_model
				->get_entry($this->campaign_id);
		}

		return false;
	}

	/**
	 * Opportunity computed property, returns parent opportunity, if exists
	 * @return Object
	 */
	public function __opportunity() {
		if( $this->campaign_id != $this->campaign_group_id ) {
			return $this->Opportunity_model
				->get_entry($this->campaign_id);
		}

		return false;
	}

	/**
	 * Custom responses computed property
	 * @return Array
	 */
	public function __custom_responses() {
		if( !$this->id ) {
			return false;
		}

		$responses = $this->db
			->select('custom_form_responses.*')
			->select('custom_texts.field_label, custom_texts.field_label, custom_texts.status')
			->where('donation_id', $this->id)
			->join('custom_texts', 'custom_texts.id = custom_form_responses.field_id', 'left')
			->get('custom_form_responses')
			->result();

		return $responses;
	}

}