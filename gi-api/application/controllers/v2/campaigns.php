<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Campaign controller
 *
 * @class Campaigns
 * @extends CI_Controller
 */
class Campaigns extends CI_Controller {

	private $required = array(
		'title',
		'description',
		'status'
	);

	private $allowed = array(
		'title',
		'description',
		'status',
		'youtube_id',
		'youtube_url',
		'enable_donation_levels',
		'donation_levels',
		'has_giving_opportunities',
		'display_total',
		'donation_target',
		'display_current',
		'display_donation_target',
		'display_donation_total',
		'image_file',
		'image_type',
		'custom_fields',
		'campaign_fields',
        'donation_minimum',
		'minimum_donation_amount',
		'receipt',
		'analytics_id',
		'campaign_color',
		'header_font_color'
	);

	public function __construct() {
		parent::__construct();

		$this->load->model('Campaign_model');
		$this->load->model('Opportunity_model');
		$this->load->model('Donation_model');
		$this->load->helper('display');
		$this->load->helper('api');
	}

	/**
	 * Index handler
	 *
	 * Handles:
	 *
	 *  * GET /api/v2/campaigns
	 *  * POST /api/v2/campaigns
	 *
	 * Accepts via GET:
	 *
	 *  * limit {Int}
	 *  * offset {Int}
	 *  * status {String}
	 *  * sort {String}
	 *
	 * Accepts via POST:
	 *
     *  * title {String}
     *  * description {String}
     *  * status {Boolean}
     *  * youtube_id {String} OR
     *  * youtube_url {String} OR
     *  * enable_donation_levels {Boolean}
     *  * donation_levels {Array}
     *  * has_giving_opportunities {Boolean}
     *  * display_total {Boolean}
     *  * donation_target {Float}
     *  * display_current {Boolean}
     *  * image_file {String} Base64 encoded
     *  * image_type {String}
     *  * custom_fields {Array}
     *  * donation_minimum {Float}
     *  * minimum_donation_amount {Float}
     *  * send_receipt {Boolean}
     *  * email_org_name {String}
     *  * reply_to_address {String}
     *  * bcc_address {String}
     *  * street_address {String}
     *  * street_address_2 {String}
     *  * city {String}
     *  * state {String}
     *  * postal_code {String}
     *  * country {String}
     *  * receipt {String}
     *  * analytics_id {String}
     *
	 * @return Array of Campaign_models
	 */
	public function index() {

		if( $this->input->post_body ) {
			$campaign = $this->_process_post(new Campaign_model);
			$campaign->account_id = $this->authorization->account;

			$campaign->save_entry();

			// just to make sure we have all the data
			$campaign = $this->Campaign_model
				->get_entry($campaign->id);

			$this->_process_donation_levels($campaign);
			$this->_process_custom_fields($campaign);
			$this->_process_campaign_fields($campaign);

			$this->load->view('v2/campaigns/single', array(
				'campaign' => array_shift(prep_campaigns(array($campaign)))
			));
			return;
		}

        if( !$this->input->post_body && $_SERVER['REQUEST_METHOD'] == 'POST' ) {
            $this->output->show_error('Your request body is missing or malformed', 400, 'bad_request');
            return;
        }

		$campaigns = api_limit_sort($this->Campaign_model)
			->where('campaigns.account_id', $this->authorization->account)
			->where('campaigns.campaign_id', NULL)
			->find();

		$this->load->view('v2/campaigns/index', array(
			'campaigns' => prep_campaigns($campaigns)
		));
	}

	/**
	 * Token handler, handles display and edit of single campaign
	 *
	 * Handles:
	 *
	 *  * GET /api/v2/campaigns/{token}
	 *  * POST /api/v2/campaigns/{token}
	 *
	 * Accepts via POST:
	 *
     *  * title {String}
     *  * description {String}
     *  * status {Boolean}
     *  * youtube_id {String} OR
     *  * youtube_url {String} OR
     *  * enable_donation_levels {Boolean}
     *  * donation_levels {Array}
     *  * has_giving_opportunities {Boolean}
     *  * display_total {Boolean}
     *  * donation_target {Float}
     *  * display_current {Boolean}
     *  * image_file {String} Base64 encoded
     *  * image_type {String}
     *  * custom_fields {Array}
     *  * donation_minimum {Float}
     *  * minimum_donation_amount {Float}
     *  * send_receipt {Boolean}
     *  * email_org_name {String}
     *  * reply_to_address {String}
     *  * bcc_address {String}
     *  * street_address {String}
     *  * street_address_2 {String}
     *  * city {String}
     *  * state {String}
     *  * postal_code {String}
     *  * country {String}
     *  * receipt {String}
     *  * analytics_id {String}
	 *
	 * @return Object single Campaign_model
	 */
	public function token() {
		if( $this->uri->rsegment(4) ) {
			$this->output->show_error('Requested resource does not exist.', 404, 'missing');
			return;
		}

		$campaigns = $this->Campaign_model
			->where('campaigns.account_id', $this->authorization->account)
			->where('campaigns.campaign_token', $this->uri->rsegment(3))
			->where('campaigns.campaign_id', NULL)
			->find();

		if( !count($campaigns) ) {
			$this->log->entry('Campaign not found for token '.$this->uri->rsegment(3));
			$this->output->show_error('Campaign not found', 404, 'invalid_token');
			return;
		}

        if( !$this->input->post_body && $_SERVER['REQUEST_METHOD'] == 'POST' ) {
            $this->output->show_error('Your request body is missing or malformed', 400, 'bad_request');
            return;
        }

		if( $this->input->post_body ) {
			$campaign = $this->_process_post($campaigns[0], false);

			$campaign->save_entry();

			// just to make sure we have all the data
			$campaign = $this->Campaign_model
				->get_entry($campaign->id);

			$this->_process_donation_levels($campaign);

			if( property_exists($this->input->post_body, 'custom_fields') ) {
				$this->_process_custom_fields($campaign);
			}
			if( property_exists($this->input->post_body, 'campaign_fields') ) {
				$this->_process_campaign_fields($campaign);
			}

			// grab a fresh copy in case the widget updated
			$campaigns = $this->Campaign_model
				->where('campaigns.id', $campaign->id)
				->find();

			$this->load->view('v2/campaigns/single', array(
				'campaign' => array_shift(prep_campaigns(array($campaigns[0])))
			));
			return;
		}



		$this->load->view('v2/campaigns/single', array(
			'campaign' => array_shift(prep_campaigns($campaigns))
		));
	}

	/**
	 * Opportunities handler, display opportunities for given campaign
	 *
	 * Handles:
	 *
	 * * GET /api/v2/campaigns/{token}/opportunities
	 *
	 * Accepts via GET:
	 *
	 *  * offset {Int}
	 *  * limit {Int}
	 *  * sort {String}
	 *  * status {String}
	 *  * related {String}
	 *
	 * @return Array of Opportunity_models
	 */
	public function opportunities() {

		$campaigns = $this->Campaign_model
			->select('id')
			->where('account_id', $this->authorization->account)
			->where('campaign_token', $this->uri->rsegment(3))
			->where('campaign_id', NULL)
			->find();

		if( !count($campaigns) ) {
			$this->log->entry('Campaign not found for token '.$this->uri->rsegment(3));
			$this->output->show_error('Campaign not found', 404, 'invalid_token');
			return;
		}

		$supporter = false;
		if( $this->input->get('supporter') ) {
        	$addr = str_replace(' ', '+', $this->input->get('supporter'));
            $supporter = $this->Supporter_model
                ->where('email_address', $addr)
                ->where('account_id', $this->authorization->account)
                ->limit(1)
                ->find();

			if( !count($supporter) ) {
				$this->log->entry('Supporter not found: '.$addr);
				$this->output->show_error('Supporter not found', 404, 'invalid_token');
				return;
			}

        }

		$opportunities = api_limit_sort($this->Opportunity_model)
			->where('campaigns.campaign_id', $campaigns[0]->id)
			->where('campaigns.account_id', $this->authorization->account);

		if( $supporter && count($supporter) ) {
            $opportunities->join('opportunities_supporters', 'opportunities_supporters.opportunity_id = campaigns.id', 'left')
            	->where('opportunities_supporters.supporter_id', $supporter[0]->id);
		}

		$opportunities = $opportunities
			->find();

		$this->load->view('v2/campaigns/opportunities', array(
			'opportunities' => prep_campaigns($opportunities)
		));
	}

	/**
	 * Display related donations
	 *
	 * Handles:
	 *
	 *  GET /api/v2/campaigns/{token}/donations
	 *
	 * Accepts via GET:
	 *
	 *  * offset {Int}
	 *  * limit {Int}
	 *  * sort {String}
	 *
	 * @return Array of Donation_model objects
	 */
	public function donations() {
		$campaigns = $this->Campaign_model
			->select('id')
			->where('account_id', $this->authorization->account)
			->where('campaign_token', $this->uri->rsegment(3))
			->where('campaign_id', NULL)
			->find();


		if( !count($campaigns) ) {
			$this->log->entry('Campaign not found for token '.$this->uri->rsegment(3));
			$this->output->show_error('Campaign not found', 404, 'invalid_token');
			return;
		}

		$supporter = false;
        if( $this->input->get('supporter') ) {
        	$addr = str_replace(' ', '+', $this->input->get('supporter'));
            $supporter = $this->Supporter_model
                ->where('email_address', $addr)
                ->where('account_id', $this->authorization->account)
                ->limit(1)
                ->find();

			if( !count($supporter) ) {
				$this->log->entry('Supporter not found: '.$addr);
				$this->output->show_error('Supporter not found', 404, 'invalid_token');
				return;
			}
        }

		$donations = api_limit_sort($this->Donation_model)
			->where('donations.campaign_group_id', $campaigns[0]->id)
			->where('donations.account_id', $this->authorization->account)
			->where('donations.complete', 1);

        if( $supporter && count($supporter) ) {
        	$donations->where('supporter_id', $supporter[0]->id);
        }

		$donations = $donations
			->find();

		$this->load->view('v2/campaigns/donations', array(
			'donations' => prep_donations($donations)
		));
	}

	private function _process_donation_levels($campaign) {
		if( !property_exists($this->input->post_body, 'donation_levels') ) {
			return;
		}

		$donation_levels = $this->input->post_body->donation_levels;
		if( !$donation_levels || !$campaign->id ) {
			return;
		}

		$p = 0;

		$keep_levels = array();
		//update or create levels
		foreach ($donation_levels as $level) {
			$p++;
			if(property_exists($level, 'level_id') && $level->level_id) {
				$this->db
					->where('id',$level->level_id)
					->update('campaign_levels', array(
						'amount'      => $level->amount,
						'description' => $level->label,
						'position'    => $level->position ? $level->position : $p,
						'campaign_id' => $campaign->id,
						'updated_at'  => date('Y-m-d g:i:s')
					));
				$keep_levels[] = $level->level_id;
			} else {
				if ($level->amount) {
					$new_level = $this->db
						->insert('campaign_levels', array(
							'amount'      => $level->amount,
							'description' => $level->label,
							'position'    => $level->position ? $level->position : $p,
							'campaign_id' => $campaign->id,
							'created_at'  => date('Y-m-d g:i:s'),
							'updated_at'  => date('Y-m-d g:i:s')
						));
					$keep_levels[] = $this->db->insert_id();
				}
			}
		}
		// delete levels -- actually make em inactive
		$campaign_levels = $this->db
			->where('campaign_id', $campaign->id)
			->get('campaign_levels')
			->result();

		$all_levels = array();

		foreach ($campaign_levels as $level) {
			$all_levels[] = $level->id;
		}

		$inactive_levels = array_diff($all_levels,$keep_levels);

		if (count($inactive_levels)) {
			foreach ($inactive_levels as $inactive) {
				$this->db
					->where('id',$inactive)
					->update('campaign_levels', array(
					'status' => 0
				));


			}
		}


	}

	private function _process_custom_fields($campaign) {
		if( !property_exists($this->input->post_body, 'custom_fields') ) {
			return;
		}

		$custom_fields = $this->input->post_body->custom_fields;

		if( !$custom_fields || !$campaign->id ) {
			return;
		}

		$form = $this->db
			->where('campaign_id', $campaign->id)
			->get('custom_forms')
			->result();

		if( count($form) ) {
			$form_id = $form[0]->id;
		} else {
			$this->db
				->insert('custom_forms', array(
					'campaign_id' => $campaign->id
				));

			$form_id = $this->db->insert_id();
		}

		foreach( $custom_fields as $field ) {
			$type = 1;
			if( $field->field_type == 'dropdown' ) {
				$type = 0;
			}

			$options = '';
			if( property_exists($field, 'options') && is_array($field->options) ) {
				$options = implode("\n", $field->options);
			}

			if( property_exists($field, 'field_id') && $field->field_id ) {
				$this->db
					->where('id', $field->field_id)
					->update('custom_texts', array(
						'field_type' => $type,
						'field_label' => $field->field_label,
						'field_options' => $options,
						'position' => $field->position,
						'status' => $field->status ? 1 : 0,
						'field_required' => $field->required ? 1 : 0,
						'custom_form_id' => $form_id,
						'created_at' => date('Y-m-d g:i:s'),
						'updated_at' => date('Y-m-d g:i:s')
					));
			} else {
				$this->db
					->insert('custom_texts', array(
						'field_type' => $type,
						'field_label' => $field->field_label,
						'field_options' => $options,
						'position' => $field->position,
						'status' => $field->status ? 1 : 0,
						'field_required' => $field->required ? 1 : 0,
						'custom_form_id' => $form_id,
						'created_at' => date('Y-m-d g:i:s'),
						'updated_at' => date('Y-m-d g:i:s')
					));
			}
		}
	}

	private function _process_campaign_fields($campaign) {
		if( !property_exists($this->input->post_body, 'campaign_fields') ) {
			return;
		}

		$campaign_fields = $this->input->post_body->campaign_fields;

		if( !$campaign_fields || !$campaign->id ) {
			return;
		}

		foreach( $campaign_fields as $field ) {
			$type = 1;
			if( $field->field_type == 'dropdown' ) {
				$type = 0;
			}

			$options = '';
			if( property_exists($field, 'options') && is_array($field->options) ) {
				$options = implode("\n", $field->options);
			}

			if( property_exists($field, 'field_id') && $field->field_id ) {
				$this->db
					->where('id', $field->field_id)
					->update('custom_campaign_fields', array(
						'field_type' => $type,
						'field_label' => $field->field_label,
						'field_options' => $options,
						'position' => $field->position,
						'status' => $field->status ? 1 : 0,
						'field_required' => $field->required ? 1 : 0,
						'campaign_id' => $campaign->id,
						'created_at' => date('Y-m-d g:i:s'),
						'updated_at' => date('Y-m-d g:i:s')
					));
			} else {
				$this->db
					->insert('custom_campaign_fields', array(
						'field_type' => $type,
						'field_label' => $field->field_label,
						'field_options' => $options,
						'position' => $field->position,
						'status' => $field->status ? 1 : 0,
						'field_required' => $field->required ? 1 : 0,
						'campaign_id' => $campaign->id,
						'created_at' => date('Y-m-d g:i:s'),
						'updated_at' => date('Y-m-d g:i:s')
					));
			}
		}
	}
	private function _process_post($campaign, $strict = true) {
		$data = json_decode(json_encode($this->input->post_body), true);

		$temp_file = false;
		$temp_file_type = false;

		// sanity
		if( $strict ) {
			foreach( $this->required as $r ) {
				if( !array_key_exists($r, $data) ) {
					$this->output->show_error('Required fields: '.implode(', ', $this->required), 400, 'missing_parameters');
					return false;
				}
			}
		}

		foreach( $data as $k => $v ) {
			if( !in_array($k, $this->allowed) ) {
				continue;
			}

			if( $k == 'campaign_token'
				|| $k == 'donation_levels'
				|| $k == 'custom_fields'
				|| $k == 'receipt' ) {
				continue;
			}

			if( $k == 'status' ) {
				if( $v ) {
					$campaign->$k = 1;
				} else {
					$campaign->$k = 0;
				}

				continue;
			} elseif( $k == 'image_file' ) {
                if( $data['image_type'] == 'nil' ) {
                    // remove the file
                    $campaign->campaign_image_file_name = '';
                    $campaign->campaign_image_content_type = '';
                    $campaign->campaign_image_file_size = 0;
                    $campaign->campaign_image_updated_at = date('Y-m-d H-i-s');
                    continue;
                }

				if( !$v ) {
					continue;
				}

				// encoded blob
				$file = base64_decode($v);

				// dump to tempfile
				$temp_file = tempnam(sys_get_temp_dir(), 'giapi');
				file_put_contents($temp_file, $file);
				$temp_file_type = $data['image_type'];

				$campaign->set_file($temp_file, $temp_file_type);

				continue;
			} elseif( $k == 'youtube_url' ) {
				$k = 'youtube';
			} elseif( $k == 'youtube_id' ) {
				$k = 'youtube';
			} elseif( $k == 'donation_target' ) {
				$k = 'target';
			} elseif( $k == 'donation_minimum' ) {
			    $k = 'minimum_donation_amount';
			} elseif( $k == 'header_font_color' ) {
				$k = 'header_font';
			}

			$campaign->$k = $this->security->xss_clean($v);
		}

		if( array_key_exists('receipt', $data) ) {
			foreach( $data['receipt'] as $k => $v ) {
				$campaign->$k = $this->security->xss_clean($v);
			}
		}

		if( array_key_exists('youtube', $data)
			&& strpos($data['youtube'], 'v=') !== false ) {
			$vars = array();
			$url = parse_url($data['youtube']);
			parse_str($url['query'], $vars);

			$campaign->youtube = $vars['v'];
		} elseif( array_key_exists('youtube', $data)
			&& strpos($data['youtube'], 'youtu.be') !== false ) {

			$campaign->youtube = str_replace('http://youtu.be/', '', $data['youtube']);
		}

		if( strpos($campaign->youtube, 'http://') === 0 ) {
			$campaign->youtube = false;
		}

		if( $campaign->minimum_donation_amount < 5 ) {
			$this->output->show_error('Minimum donation must be 5.00 or greater', 400, 'invalid_parameters');
			return false;
		}
		return $campaign;
	}
}