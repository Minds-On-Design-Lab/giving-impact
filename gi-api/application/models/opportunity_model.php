<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Opportunity model
 */
class Opportunity_model extends GI_Model {

	public $id                          = false;
	public $campaign_id                 = false;
	public $campaign_token              = NULL;
	public $current                     = false;
	public $description                 = false;
	public $campaign_image_file_name    = NULL;
	public $target                      = false;
	public $title                       = false;
	public $youtube                     = NULL;
	public $account_id                  = NULL;
	public $created_at                  = NULL;
	public $updated_at                  = NULL;
	public $enable_donation_levels      = 0;
	public $status                      = 1;
	public $total_donations 			= 0;

	protected $_table = 'campaigns';

	protected $_temp_file = false;
	protected $_temp_file_type = 'jpg';

	protected $_temp_campaign_responses = false;

	protected $_my_campaign = false;

	public function __construct() {
		parent::__construct();

		$this->load->model('Campaign_model');
		$this->load->library('image_lib');
		$this->load->model('Supporter_model');

	}

	/**
	 * Set campaign fields regardless of opp save state
	 * @param array $fields
	 */
	public function set_campaign_responses($fields) {
		$this->_temp_campaign_responses = $fields;
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
	 * After saving new opportunity, automatically generates widget
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


		parent::save_entry();

		if( $this->_temp_file ) {
			$this->_process_file();
		}

		if( $this->_temp_campaign_responses ) {
			$this->_process_campaign_responses();
		}
	}

	public function __supporters() {

		$res = $this->db
			->where('opportunity_id', $this->id)
			->get('opportunities_supporters')
			->result();

		$supporters = array();
		foreach( $res as $row ) {
			$supporter = $this->Supporter_model
				->where('account_id', $this->account_id)
				->where('id', $row->supporter_id)
				->find();
			if( $supporter && count($supporter) ) {
				$s = $supporter[0];
				$s->date_added = $row->created_at;
				$supporters[] = $s;
			}
		}

		return $supporters;
	}

	/**
	 * campaign computed property
	 * @return Object parent campaign
	 */
	public function __campaign() {
		if( !$this->_my_campaign ) {
			$this->_my_campaign = $this->Campaign_model
				->get_entry($this->campaign_id);
		}

		return $this->_my_campaign;
	}

	/**
	 * Custom fields computed property
	 * @return Array custom fields
	 */
	public function __custom_fields() {
		$row = $this->db
			->where('campaign_id', $this->id)
			->get('custom_forms')
			->result();
		if( !count($row) || !$row[0] || !$row[0]->id ) {
			return $this->campaign->custom_fields;
		}

		$fields = $this->db
			->where('custom_form_id', $row[0]->id)
			->get('custom_texts')
			->result();

		return $fields;
	}

	/**
	 * Campaign levels computed property
	 * @return Array of campaign levels
	 */
	public function __donation_levels() {
		if( !$this->enable_donation_levels ) {
			return false;
		}

		$levels = $this->db
			->where('campaign_id', $this->id)
			->get('campaign_levels')
			->result();
		if( !count($levels) ) {
			return false;
		}

		return $levels;

	}

	public function __campaign_responses() {
		if( !$this->id ) {
			return false;
		}

		$responses = $this->db
			->select('custom_campaign_field_responses.*')
			->select('custom_campaign_fields.field_label, custom_campaign_fields.field_type, custom_campaign_fields.status')
			->where('opportunity_id', $this->id)
			->join('custom_campaign_fields', 'custom_campaign_fields.id = custom_campaign_field_responses.campaign_field_id', 'left')
			->get('custom_campaign_field_responses')
			->result();

		return $responses;
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

	private function _process_campaign_responses() {
		if( !$this->_temp_campaign_responses ) {
			return false;
		}

		$fields = $this->_temp_campaign_responses;
		$this->_temp_campaign_responses = false;

		$existing = array();
		foreach( $this->campaign->campaign_fields as $f ) {
			if( !$f->status ) {
				continue;
			}
			$existing[] = $f->id;
		}

		$this->db
			->where('opportunity_id', $this->id)
			->delete('custom_campaign_field_responses');

		foreach( $fields as $field ) {
            $data = new stdClass;
            $data->user_response    = $field['response'];
            $data->campaign_field_id= $field['campaign_field_id'];
            $data->opportunity_id   = $this->id;
            $data->campaign_id      = $this->campaign_id;
            $data->created_at       = date('Y-m-d H:m:s');
            $data->updated_at       = $data->created_at;

            $this->db
                ->insert('custom_campaign_field_responses', $data);
		}
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
				'Body'   => file_get_contents(sys_get_temp_dir().'/'.$this->id.'_thumb.'.$ext),
				'ACL'    => 'public-read',
			));

			$s3Client->putObject(array(
				'Bucket' => $this->config->item('s3_bucket'),
				'Key'    => $this->id.'/_original.'.$ext,
				'Body'   => file_get_contents(sys_get_temp_dir().'/'.$this->id.'_original.'.$ext),
				'ACL'    => 'public-read',
			));
		} else {
            $store_path = rtrim(FCPATH, '/').'/../uploads/files/';

            if (!file_exists($store_path)) {
                mkdir($store_path, 0755, true);
            }

            file_put_contents(
                $store_path.'logos-'.$this->id.'-_thumb.'.$ext,
                file_get_contents(sys_get_temp_dir().'/'.$this->id.'_thumb.'.$ext)
            );

            file_put_contents(
                $store_path.'logos-'.$this->id.'-_original.'.$ext,
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