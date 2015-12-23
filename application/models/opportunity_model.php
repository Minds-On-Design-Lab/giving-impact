<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Opportunity model
 *
 * @class Opportunity_model
 * @extends  GI_Model
 */
class Opportunity_model extends GI_Model {

	public $id = false;
	public $campaign_id = false;
	public $campaign_token = false;
	public $current = false;
	public $description = false;
	public $campaign_image_file_name = false;
	public $target = false;
	public $title = false;
	public $status = false;
	public $youtube = false;
	public $account_id = false;
	public $created_at = false;
	public $updated_at = false;
	public $total_donations = 0;
	//public $has_donation_levels = false;

	protected $_table = 'campaigns';

	protected $_myparent = false;
	protected $_thumb_url = false;
	protected $_image_url = false;

	public function __construct() {
		parent::__construct();

		$this->load->model('Campaign_model');
	}

	public function find($tbl = null, $klass = null) {
		$this->select(
			'campaigns.'.implode(
				', campaigns.', $this->get_public_properties()
			)
		);

		return parent::find($tbl, $klass);
	}

	public function get_entry($uid) {
		$this->select(
			'campaigns.'.implode(
				', campaigns.', $this->get_public_properties()
			)
		);

		return parent::get_entry($uid);
	}

/*
	public function save_entry() {
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
	 * Parent campaign computed property
	 * @return Object
	 */
	public function __campaign() {
	    if( $this->_myparent ) {
	        return $this->_myparent;
	    }
		$this->_myparent = $this->Campaign_model
			->get_entry($this->campaign_id);

		return $this->_myparent;
	}

    public function __frequency_type() {
    	return $this->__campaign()->frequency_type;
    }
    public function __frequency_period() {
    	return $this->__campaign()->frequency_period;
    }

	/**
	 * Custom fields computed property, returns parent campaign fields
	 * @return Array
	 */
	public function __custom_fields() {
        return $this->__campaign()->custom_fields();
	}

	/**
	 * Campaign levels computed property, returns parent campaign levels
	 * @return Array
	 */
	public function __donation_levels() {
        return $this->__campaign()->__donation_levels();
	}

	/**
	 * header font computed property, returns parent campaign header font
	 * @return String
	 */
	public function __header_font() {
	    return $this->__campaign()->header_font;
	}

	/**
	 * campaign_color  computed property, returns parent campaign accent
	 * @return String
	 */
	public function __campaign_color() {
	    return $this->__campaign()->campaign_color;
	}

	/**
	 * has campaign levels computed property, returns parent campaign info
	 * @return Boolean
	 */
	public function __enable_donation_levels() {
	    return $this->__campaign()->enable_donation_levels;
	}

	/**
	 * min donation amount computed property, returns parent campaign info
	 * @return Int
	 */
	public function __minimum_donation_amount() {
	    return $this->__campaign()->minimum_donation_amount;
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
	 * Thumbnail computed property
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
		        $this->_image_url = 'https://s3.amazonaws.com/'.$this->config('s3_bucket').'/'.$this->id.'/_original.'.$ext;
		        $this->_thumb_url = 'https://s3.amazonaws.com/'.$this->config('s3_bucket').'/'.$this->id.'/_thumb.'.$ext;
		    } else {
		        $this->_image_url = base_url('uploads/files/'.$this->id.'/_original.'.$ext);
		        $this->_thumb_url = base_url('uploads/files/'.$this->id.'/_thumb.'.$ext);
		    }
	    }
	}
}
