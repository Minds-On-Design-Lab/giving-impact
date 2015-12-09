<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Campaign model
 *
 */
class Campaign_model extends GI_Model {

	public $id                          = false;
	public $campaign_token              = NULL;
	public $current                     = false;
	public $description                 = false;
	public $campaign_image_file_name    = NULL;
	public $target                      = false;
	public $title                       = false;
	public $has_giving_opportunities    = 0;
	public $youtube                     = NULL;
	public $status                      = 1;
	public $created_at                  = NULL;
	public $updated_at                  = NULL;
	public $enable_donation_levels      = 0;
	public $account_id                  = NULL;
	public $minimum_donation_amount     = 0.00;
    public $send_receipt                = 0;
    public $email_org_name              = NULL;
    public $reply_to_address            = NULL;
    public $bcc_address                 = NULL;
    public $street_address              = NULL;
    public $street_address_2            = NULL;
    public $city                        = NULL;
    public $state                       = NULL;
    public $postal_code                 = NULL;
    public $country                     = NULL;
    public $receipt_body                = NULL;
    public $analytics_id				= NULL;
    public $total_donations				= 0;
    public $total_opportunities			= 0;
    public $display_donation_target		= 1;
    public $display_donation_total		= 1;
    // public $header_font					= NULL;
    // public $campaign_color				= NULL;
    public $frequency_type              = 0;
    public $frequency_period            = 0;

	protected $_temp_file = false;
	protected $_temp_file_type = 'jpg';

	public function __construct() {
		parent::__construct();

		$this->load->library('image_lib');
	}

	/**
	 * Set new file attached to campaign
	 * @param String $file file path
	 * @param String $type MIME type
	 */
	public function set_file($file, $type) {
		$this->_temp_file = $file;
		$this->_temp_file_type = $type;
	}

	/**
	 * Extends GI_Model save_entry method. Automatically generates
	 *
	 * * campaign_token
	 * * created_at
	 * * updated_at
	 *
	 * After saving new campaign, automatically generates widget
	 * data and processes uploaded files
	 *
	 * @return Object this
	 */
	public function save_entry() {
		if( !$this->id ) {
			$this->campaign_token = api_generate_token();
		}

		$this->updated_at = date('Y-m-d H:i:s');

		if( !$this->created_at ) {
			$this->created_at = date('Y-m-d H:i:s');
		}

		if( !$this->id ) {
			$this->header_font		= '#ffffff';
			$this->campaign_color	= '#25aae1';
		}

		parent::save_entry();

		if( $this->_temp_file ) {
			$this->_process_file();
		}
	}

	/**
	 * Custom fields computed property
	 * @return Array of custom fields
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

		return $fields;
	}

	/**
	 * Campaign create custom fields computed property
	 * @return Array of custom fields
	 */
	public function __campaign_fields() {

		$fields = $this->db
			->where('campaign_id', $this->id)
			->get('custom_campaign_fields')
			->result();

		return $fields;
	}

	/**
	 * Campaign levels computed property
	 * @return Array of campaign levels
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

		return $levels;

	}

	private function _generate_links() {
		// links
		$gi_url = $this->config->item('base_gi_url');

		$short_tok = api_generate_token();
		$links = array(
			'website_url' => $gi_url.'initiate_donation/'.$this->campaign_token,
			'short_token' => $short_tok,
			'campaign_id' => $this->id,
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		);
		$this->db->insert('links', $links);
	}

	private function _process_file() {
		if( !$this->_temp_file ) {
			return false;
		}

		$temp_file = $this->_temp_file;
		$type = $this->_temp_file_type;

		$this->_temp_file = false;

		switch($type) {
			case 'png':
				$ext = 'png';
				break;
			case 'gif':
				$ext = 'gif';
				break;
			case 'jpg':
			default:
				$ext = 'jpg';
				break;
		}

		$config = array(
			'source_image' => $temp_file,
			'new_image' => $this->id.'_thumb.'.$ext,
			'maintain_ratio' => TRUE,
			'width' => 135,
			'height' => 90
		);

		$this->image_lib->initialize($config);
		if( !$this->image_lib->resize() ) {
			return false;
		}

		$this->image_lib->clear();

		$config = array(
			'source_image' => $temp_file,
			'new_image' => $this->id.'_original.'.$ext,
			'maintain_ratio' => TRUE,
			'width' => 300,
			'height' => 200
		);

		$this->image_lib->initialize($config);
		$this->image_lib->resize();

		if ($this->config->item('s3_bucket')) {
			$s3Client = \Aws\S3\S3Client::factory(array(
				'credentials' => array(
					'key'    => $this->config->item('s3_access_key'),
					'secret' => $this->config->item('s3_secret_key'),
				)
			));

			$s3Client->putObject(array(
				'Bucket' => $this->config->item('s3_bucket'),
				'Key'    => $this->id.'/_thumb.'.$ext,
				'Body'   => file_get_contents(sys_get_temp_dir().'/'.$this->id.'/_thumb.'.$ext),
				'ACL'    => 'public-read',
			));

			$s3Client->putObject(array(
				'Bucket' => $this->config->item('s3_bucket'),
				'Key'    => $this->id.'/_original.'.$ext,
				'Body'   => file_get_contents(sys_get_temp_dir().'/'.$this->id.'/_original.'.$ext),
				'ACL'    => 'public-read',
			));
		} else {
            $store_path = rtrim(FCPATH, '/').'/uploads/files/campaigns';

            if (!file_exists($store_path)) {
                mkdir($store_path, 0755, true);
            }

            if (!file_exists($store_path.'/'.$this->id)) {
                mkdir($store_path.'/'.$this->id, 0755, true);
            }

            file_put_contents(
                $store_path.'/'.$this->id.'/_thumb.'.$ext,
                file_get_contents(sys_get_temp_dir().'/'.$this->id.'_thumb.'.$ext)
            );

            file_put_contents(
                $store_path.'/'.$this->id.'/_original.'.$ext,
                file_get_contents(sys_get_temp_dir().'/'.$this->id.'_original.'.$ext)
            );
		}

		$this->campaign_image_file_name = $this->id.'.'.$ext;
		$this->campaign_image_content_type = 'image/'.$ext;
		$this->campaign_image_file_size = filesize($temp_file);
		$this->campaign_image_updated_at = date('Y-m-d H-i-s');

		@unlink($temp_file);
		@unlink($this->id.'_thumb.'.$ext);
		@unlink($this->id.'_original.'.$ext);

		$this->save_entry();

	}

}
