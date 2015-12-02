<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Campaign model
 *
 * @class Campaign_model
 * @extends  GI_Model
 */
class Campaign_model extends GI_Model {

	public $id = false;
	public $campaign_token = false;
	public $campaign_id = false;
	public $current = false;
	public $description = false;
	public $campaign_image_file_name = false;
	public $target = false;
	public $title = false;
	public $has_giving_opportunities = false;
	public $youtube = false;
	public $status = false;
	public $created_at = false;
	public $updated_at = false;
	public $enable_donation_levels = false;
	public $account_id = false;
	public $minimum_donation_amount = false;
    public $send_receipt = false;
    public $email_org_name = false;
    public $reply_to_address = false;
    public $bcc_address = false;
    public $street_address = false;
    public $street_address_2 = false;
    public $city = false;
    public $state = false;
    public $postal_code = false;
    public $country = false;
    public $receipt_body = false;
    public $analytics_id = false;
    public $total_donations = 0;
    public $total_opportunities = 0;
    public $display_donation_target		= 1;
    public $display_donation_total		= 1;
    public $header_font					= NULL;
    public $campaign_color				= NULL;

    private $_thumb_url = false;
    private $_image_url = false;

	public function __construct() {
		parent::__construct();

	}

/*	public function save_entry() {
		if( !$this->id ) {
			$this->campaign_token = api_generate_token();
		}

		$this->updated_at = date('Y-m-d H:i:s');

		if( !$this->created_at ) {
			$this->created_at = date('Y-m-d H:i:s');
		}

		$do_generate_widget = false;
		if( !$this->id ) {
			$do_generate_widget = true;
		}

		parent::save_entry();

		if( $do_generate_widget ) {
			$this->_generate_widget();
			$this->_generate_links();
		}

		if( $this->_temp_file ) {
			$this->_process_file();
		}
	}
*/

	/**
	 * Campaign computed property, always returns FALSE
	 * @return Boolean
	 */
	public function __campaign() {
		return false;
	}

    /**
     * Custom campaign fields computed property
     * @return Array
     */
    public function __campaign_fields() {
        return false;
    }

	/**
	 * Custom fields computed property
	 * @return Array
	 */
	public function __custom_fields() {
		$row = $this->db
			->where('campaign_id', $this->id)
			->get('custom_forms')
			->result();
		if( !count($row) || !$row[0] || !$row[0]->id ) {
			return false;
		}

		$fields = $this->db
			->where('custom_form_id', $row[0]->id)
			->get('custom_texts')
			->result();

		if( $fields ) {
			$out = array();
			foreach( $fields as $field ) {
				$out[] = array(
					'field_id' => $field->id,
					'field_type' => $field->field_type ? 'text' : 'dropdown',
					'field_label' => $field->field_label,
					'options' => $field->field_type ? array() : explode("\n", $field->field_options),
					'position' => $field->position,
					'status' => $field->status ? true : false,
					'required' => $field->field_required ? true : false
				);
			}

            return $out;
		}

		return false;
	}

	/**
	 * Campaign levels computed property
	 * @return Array
	 */
	public function __donation_levels() {
//		if( !$this->has_donation_levels ) {
//			return false;
//		}

		$levels = $this->db
			->where('campaign_id', $this->id)
			->get('campaign_levels')
			->result();
		if( !count($levels) ) {
			return false;
		}

		if( is_array($levels) ) {
			$out = array();

			foreach( $levels as $lvl ) {
				$out[] = array(
				    'level_id' => $lvl->id,
					'amount' => $lvl->amount,
					'label' => $lvl->description,
					'position' => $lvl->position,
                    'status' => $lvl->status
				);
			}

			return $out;
		}

		return false;
	}

	/**
	 * Image url computed property
	 * @return String
	 */
    public function __image_url() {
	    if( !$this->campaign_image_file_name ) {
	        return false;
	    }

        if( $this->campaign_image_file_name && $this->_image_url ) {
		    return $this->_image_url;
		}

		$this->_fetch_urls();
        return $this->_image_url;
    }

    /**
     * Thumbnail url computed property
     * @return String
     */
    public function __thumb_url() {
	    if( !$this->campaign_image_file_name ) {
	        return false;
	    }

        if( $this->campaign_image_file_name && $this->_thumb_url ) {
		    return $this->_thumb_url;
		}

		$this->_fetch_urls();
        return $this->_thumb_url;
    }

    private function _fetch_urls() {
		if( $this->campaign_image_file_name ) {

			$ext = substr(
				$this->campaign_image_file_name,
				strrpos($this->campaign_image_file_name, '.')+1
			);

            if ($this->config->item('s3_bucket')) {
    			$this->_image_url = 'https://s3.amazonaws.com/'.$this->config->item('s3_bucket').'/'.$this->id.'/_original.'.$ext;
    			$this->_thumb_url = 'https://s3.amazonaws.com/'.$this->config->item('s3_bucket').'/'.$this->id.'/_thumb.'.$ext;
            } else {
                $this->_image_url = base_url('uploads/files/'.$this->id.'/_original.'.$ext);
                $this->_thumb_url = base_url('uploads/files/'.$this->id.'/_thumb.'.$ext);
            }
		}
    }

}