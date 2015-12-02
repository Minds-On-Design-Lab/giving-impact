<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Donation model
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
	public $description = false;
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
	public $stripe_charge_id = false;
	public $supporter_id = null;
	public $refunded = 0;

	private $_custom_responses = false;

	public function __construct() {
		parent::__construct();

		$this->load->model('Campaign_model');
		$this->load->model('Opportunity_model');
		$this->load->model('Supporter_model');
	}

	/**
	 * Ensures the proper joins and linked data are selected
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
	 * Extends GI_Model save_entry method. Automatically generates
	 *
	 * * donation_token
	 * * created_at
	 * * updated_at
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

		parent::save_entry();


	    if( is_array($this->_custom_responses) && count($this->_custom_responses) ) {

	        foreach( $this->_custom_responses as $id => $resp ) {
	            $field = $this->db
	                ->where('id', $id)
	                ->get('custom_texts')
	                ->result();

	            $existing = $this->db
	            	->select('id')
	            	->where('field_id', $id)
	            	->where('donation_id', $this->id)
	            	->limit(1)
	            	->get('custom_form_responses')
	            	->result();

	            $data = new stdClass;
	            $data->user_response    = $resp['response'];
	            $data->field_id         = $id;
	            $data->donation_id      = $this->id;
	            $data->created_at       = date('Y-m-d H:m:s');
	            $data->updated_at       = $data->created_at;
	            $data->field_label      = $field[0]->field_label;

	            if( $existing ) {
	            	$this->db
	            		->where('id', $existing[0]->id)
	            		->update('custom_form_responses', $data);
	            } else {
		            $this->db
		                ->insert('custom_form_responses', $data);
	            }
	        }
	    }

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
	 * Campaign computed property
	 * @return Object parent campaign
	 */
	public function __campaign() {
		if( $this->campaign_id == $this->campaign_group_id ) {
			return $this->Campaign_model
				->get_entry($this->campaign_id);
		}

		return false;
	}

	/**
	 * Opportunity computed property
	 * @return Object parent opportunity
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
	 * @return Array responses
	 */
	public function __custom_responses() {
		if( !$this->id ) {
			return false;
		}

		$responses = $this->db
			->select('custom_form_responses.*')
			->select('custom_texts.field_type as parent_field_type, custom_texts.field_label, custom_texts.field_label, custom_texts.status')
			->where('donation_id', $this->id)
			->join('custom_texts', 'custom_texts.id = custom_form_responses.field_id', 'left')
			->get('custom_form_responses')
			->result();

		return $responses;
	}

	public function set_responses($resp) {
		$out = array();
		foreach( $resp as $item ) {
			if( !is_array($item) ) {
				continue;
			}
			$out[$item['field_id']] = $item;
		}

		$this->_custom_responses = $out;
	}

}