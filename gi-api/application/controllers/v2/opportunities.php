<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Opportunities controller
 *
 * @class Opportunities
 * @extends CI_Controller
 */
class Opportunities extends CI_Controller {

	private $required = array(
		'title',
		'description',
		'status'
	);

	private $allowed = array(
		'title',
		'description',
		'status',
		'donation_target',
		'target',
		'image_file',
		'youtube_url',
		'youtube_id',
		'youtube',
		'campaign_token',
		'campaign_responses',

		'supporters'
	);

	public function __construct() {
		parent::__construct();

		$this->load->model('Campaign_model');
		$this->load->model('Opportunity_model');
		$this->load->model('Donation_model');
        $this->load->model('Task_model');
        $this->load->model('Hook_model');
        $this->load->model('Supporter_model');

		$this->load->helper('display');
		$this->load->helper('api');
	}

	/**
	 * Index handler.
	 *
	 * Handles:
	 *
	 *  * GET /api/v2/opportunities
	 *  * POST /api/v2/opportunities
	 *
	 * Handles creation of new opportunities.
	 * Expects parent 'campaign_token' to be part of POSTed data.
	 *
	 * Accepts via GET:
	 *
	 *  * limit {Int}
	 *  * offset {Int}
	 *  * related {String}
	 *  * status {String}
	 *  * sort {String}
	 *
	 * Accepts via POST:
	 *
	 *  * title {String}
	 *  * description {String}
	 *  * status {Boolean}
	 *  * donation_target {Float}
	 *  * target {Float}
	 *  * image_file {String} Base64 encoded string
	 *  * youtube_url {String} OR
	 *  * youtube_id {String} OR
	 *  * youtube {String}
	 *  * campaign_token {String}
	 *
	 * @return Object Opportunity_model object
	 */
	public function index() {

		if( !$this->input->post_body ) {
			if( $this->input->get('supporter') ) {

				$id = $this->input->get('supporter');
				if( strpos($id, '@') !== false ) {
					$supporter = $this->Supporter_model
						->where('email_address', $id)
						->where('account_id', $this->authorization->account)
						->limit(1)
						->find();
				} else {
					$supporter = $this->Supporter_model
						->where('supporter_token', $id)
						->where('account_id', $this->authorization->account)
						->limit(1)
						->find();
				}

				if( count($supporter) ) {
					$supporter = $supporter[0];

		            $opportunities = api_limit_sort($this->Opportunity_model)
		                ->where('campaigns.account_id', $this->authorization->account)
			            ->where('opportunities_supporters.supporter_id', $supporter->id)
			            ->join('opportunities_supporters', 'opportunity_id = campaigns.id', 'left')
		                ->find();

					return $this->load->view('v2/opportunities/index', array(
						'opportunities' => prep_campaigns($opportunities)
					));
				}

				$this->output->show_error('No supporter found', 400, 'missing_supporter');
				return;

			} else {

	            $opportunities = api_limit_sort($this->Opportunity_model)
	                ->where('campaigns.account_id', $this->authorization->account)
	                ->find();

				return $this->load->view('v2/opportunities/index', array(
					'opportunities' => prep_campaigns($opportunities)
				));
			}
		}

		$data = $this->input->post_body;

		if( !property_exists($this->input->post_body, 'campaign_token') ) {
			$this->output->show_error('Campaign token is required', 404, 'invalid_token');
			return;
		}

		$campaigns = $this->Campaign_model
			->where('account_id', $this->authorization->account)
			->where('campaign_token', $this->input->post_body->campaign_token)
			->where('campaign_id', NULL)
			->find();

		if( !count($campaigns) ) {
			$this->log->entry('Invalid campaign token');
			$this->output->show_error('Campaign not found', 404, 'invalid_token');
			return;
		}

		$campaign = $campaigns[0];

		if( !$campaign->has_giving_opportunities ) {
			$this->log->entry('Campaign does not have giving opportunities');
			$this->output->show_error('Campaign does not allow giving opportunities', 402);
			return;
		}

		$opp = $this->_process_post(new Opportunity_model);
		if( !$opp ) {
			return;
		}

		$opp->campaign_id = $campaign->id;
		$opp->account_id = $this->authorization->account;
		$opp->has_giving_opportunities = 0;

		$supporters = array();

		if( property_exists($data, 'supporters') && is_array($data->supporters) ) {

			foreach( $data->supporters as $s ) {

				if( !property_exists($s, 'email_address') ) {
					continue;
				}

				$addr = $s->email_address;

				if( !$addr ) {
					continue;
				}

		        $supporter = $this->Supporter_model
		            ->where('account_id', $this->authorization->account)
		            ->where('email_address', $addr)
		            ->find();

		        if( !$supporter || !count($supporter) ) {
		            $supporter = new Supporter_model;
		            $supporter->account_id          = $this->authorization->account;
					$supporter->email_address 		= $addr;
		        } else {
		            $supporter = $supporter[0];
		        }

				$check = array(
					'first_name',
					'last_name',
					'street_address',
					'city',
					'state',
					'postal_code',
					'country'
				);
				foreach( $check as $k ) {
					if( property_exists($s, $k) ) {
						$supporter->$k = $s->$k;
					}
				}

				$supporter->save_entry();
				$supporters[] = $supporter;

			}
		}

		$opp->save_entry();

		if( $supporters ) {
			foreach( $supporters as $supporter ) {
				$res = $this->db
					->where('supporter_id', $supporter->id)
					->where('opportunity_id', $opp->id)
					->get('opportunities_supporters')
					->result();

				if( !count($res) ) {
					$res = $this->db
						->where('opportunity_id', $opp->id)
						->get('opportunities_supporters')
						->result();
					if( !count($res) ) {
						$this->db->insert(
							'opportunities_supporters',
							array(
								'opportunity_id'	=> $opp->id,
								'supporter_id'		=> $supporter->id,
								'is_lead'			=> 1,
								'created_at'		=> date('Y-m-d H:i:s')
							)
						);
					} else {
						$this->db->insert(
							'opportunities_supporters',
							array(
								'opportunity_id'	=> $opp->id,
								'supporter_id'		=> $supporter->id,
								'created_at'		=> date('Y-m-d H:i:s')
							)
						);
					}
				}
			}
		}

		$campaign->updated_at = date('Y-m-d H:i:s');
		$campaign->total_opportunities = $campaign->total_opportunities+1;
		$campaign->save_entry();

        // check for hooks and generate a task call
        $hooks = $this->Hook_model
            ->where('account_id', $this->authorization->account)
            ->where('status', true)
            ->where('event', 'opportunity.create')
            ->find();
        foreach( $hooks as $hook ) {
            $task = $this->Task_model
                ->createTask($hook, $opp);
            $task->save_entry();
        }

		$this->load->view('v2/opportunities/single', array(
			'opportunity' => array_shift(prep_campaigns(array($opp)))
		));

	}

	/**
	 * Handles request for single opportunity and editing single opportunity
	 *
	 * Handles:
	 *
	 *  * GET /api/v2/opportunities/{hash}
	 *  * POST /api/v2/opportunities/{hash}
	 *
	 * Accepts via GET:
	 *
	 *  * limit {int}
	 *  * offset {int}
	 *  * related {string}
	 *
	 * Accepts via POST:
	 *
	 *  * title {String}
	 *  * description {String}
	 *  * status {Boolean}
	 *  * donation_target {Float}
	 *  * target {Float}
	 *  * image_file {String} Base64 encoded string
	 *  * youtube_url {String} OR
	 *  * youtube_id {String} OR
	 *  * youtube {String}
	 *  * campaign_token {String}
	 *
	 * @return Object single Opportunity_model
	 */
	public function token() {
		if( $this->uri->rsegment(4) ) {
			$this->output->show_error('Requested resource does not exist.', 404, 'missing');
			return;
		}

		$opps = $this->Opportunity_model
			->where('campaigns.account_id', $this->authorization->account)
			->where('campaigns.campaign_token', $this->uri->rsegment(3));

		if( $this->input->get('supporter') ) {
            $supporter = $this->Supporter_model
                ->where('email_address', $id)
                ->where('account_id', $this->authorization->account)
                ->limit(1)
                ->find();

            $opps->join('opportunities_supporters', 'opportunities_supporters.opportunity_id = campaigns.id', 'left')
            	->where('opportunities_supporters.supporter_id', $supporter[0]->id);
		}

		$opps = $opps
			->find();

		if( !count($opps) ) {
			$this->log->entry('Opportunity not found for token '.$this->uri->rsegment(3));
			$this->output->show_error('Opportunity not found', 404, 'invalid_token');
			return;
		}

		if( !$opps[0]->campaign_id ) {
			// not an opportunity
			$this->output->show_error('Oppportunity not found', 404, 'invalid_token');
			return;
		}

        if( !$this->input->post_body && $_SERVER['REQUEST_METHOD'] == 'POST' ) {
            $this->output->show_error('Your request body is missing or malformed', 400, 'bad_request');
            return;
        }

		if( $this->input->post_body ) {

			$opp = $this->_process_post($opps[0]);
			if( !$opp ) {
				return;
			}
			$opp = $this->Opportunity_model->assign($opp);

			$supporters = array();
			$supporter_ids = array();

			if( property_exists($this->input->post_body, 'supporters') ) {

				foreach( $this->input->post_body->supporters as $s ) {

					if( !property_exists($s, 'email_address') ) {
						continue;
					}

					$addr = $s->email_address;

					if( !$addr ) {
						continue;
					}

			        $supporter = $this->Supporter_model
			            ->where('account_id', $this->authorization->account)
			            ->where('email_address', $addr)
			            ->find();

			        if( !$supporter || !count($supporter) ) {
			            $supporter = new Supporter_model;
			            $supporter->account_id          = $this->authorization->account;
						$supporter->email_address 		= $addr;
			        } else {
			            $supporter = $supporter[0];
			        }

					$check = array(
						'first_name',
						'last_name',
						'street_address',
						'city',
						'state',
						'postal_code',
						'country'
					);
					foreach( $check as $k ) {
						if( property_exists($s, $k) ) {
							$supporter->$k = $s->$k;
						}
					}

					$supporter->save_entry();
					$supporters[] = $supporter;
					$supporter_ids[] = $supporter->id;

				}
			}

			// resetting for sanity reasons
			$opp->has_giving_opportunities = 0;
			$opp->save_entry();

			if( $supporters ) {
				foreach( $supporters as $supporter ) {
					$res = $this->db
						->where('supporter_id', $supporter->id)
						->where('opportunity_id', $opp->id)
						->get('opportunities_supporters')
						->result();

					if( !count($res) ) {
						$res = $this->db
							->where('opportunity_id', $opp->id)
							->get('opportunities_supporters')
							->result();
						if( !count($res) ) {
							$this->db->insert(
								'opportunities_supporters',
								array(
									'opportunity_id'	=> $opp->id,
									'supporter_id'		=> $supporter->id,
									'is_lead'			=> 1,
									'created_at'		=> date('Y-m-d H:i:s')
								)
							);
						} else {
							$this->db->insert(
								'opportunities_supporters',
								array(
									'opportunity_id'	=> $opp->id,
									'supporter_id'		=> $supporter->id,
									'created_at'		=> date('Y-m-d H:i:s')
								)
							);
						}
					}
				}
			}

			// remove and removed supporters
			$res = $this->db
				->select('id, supporter_id')
				->where('opportunity_id', $opp->id)
				->get('opportunities_supporters')
				->result();
			foreach( $res as $r ) {
				if( !in_array($r->supporter_id, $supporter_ids) ) {
					$this->db
						->where('id', $r->id)
						->delete('opportunities_supporters');
				}
			}


	        // check for hooks and generate a task call
	        $hooks = $this->Hook_model
	            ->where('account_id', $this->authorization->account)
	            ->where('status', true)
	            ->where('event', 'opportunity.edit')
	            ->find();
	        foreach( $hooks as $hook ) {
	            $task = $this->Task_model
	                ->createTask($hook, $opp);
	            $task->save_entry();
	        }

			$opps = array($opp);
		}


		$this->load->view('v2/opportunities/single', array(
			'opportunity' => array_shift(prep_campaigns($opps))
		));
	}

	/**
	 * Handles display of related donations
	 *
	 * Handles:
	 *
	 *  * GET /api/v2/opportunities/{hash}/donations
	 *
	 * @return Array of Donation_model objects
	 */
	public function donations() {
		$campaigns = $this->Campaign_model
			->select('id, campaign_id')
			->where('account_id', $this->authorization->account)
			->where('campaign_token', $this->uri->rsegment(3))
			->find();

		if( !count($campaigns) ) {
			$this->log->entry('Campaign not found for token '.$this->uri->rsegment(3));
			$this->output->show_error('Campaign not found', 404, 'invalid_token');
			return;
		}

		if( !$campaigns[0]->campaign_id ) {
			// not an opportunity
			$this->output->show_error('Oppportunity not found', 404, 'invalid_token');
		}

		$supporter = false;
		if( $this->input->get('supporter') ) {
			$addr = str_replace(' ', '+', $this->input->get('supporter'));
            $supporter = $this->Supporter_model
                ->where('email_address', $addr)
                ->where('account_id', $this->authorization->account)
                ->limit(1)
                ->find();

			if( !$supporter ) {
				$this->log->entry('Supporter not found: '.$addr);
				$this->output->show_error('Supporter not found', 404, 'invalid_token');
				return;
			}
		}

		$donations = api_limit_sort($this->Donation_model)
			->select_complete();


       	if( $supporter && count($supporter) ) {
       		$donations->where('donations.supporter_id', $supporter[0]->id);
       	}

		$donations = $donations
			->where('donations.campaign_id', $campaigns[0]->id)
			->where('donations.account_id', $this->authorization->account)
			->where('donations.complete', 1)
			->find();

		$this->load->view('v2/campaigns/donations', array(
			'donations' => prep_donations($donations)
		));
	}

	/**
	 * processes the inbound opportunity data
	 * @param  Object $opp
	 * @return Object
	 */
	public function _process_post($opp) {
		$data = json_decode(json_encode($this->input->post_body), true);

		$temp_file = false;
		$temp_file_type = false;

		// sanity
		foreach( $this->required as $r ) {
			if( !array_key_exists($r, $data) ) {
				$this->output->show_error('Required fields: '.implode(', ', $this->required), 400, 'missing_parameters');
				return false;
			}
		}

		foreach( $data as $k => $v ) {
			if( !in_array($k, $this->allowed) ) {
				continue;
			}

			if( $k == 'campaign_token' || $k == 'campaign_responses' ) {
				continue;
			}

			if( $k == 'status' ) {
				if( $v ) {
					$opp->$k = 1;
				} else {
					$opp->$k = 0;
				}

				continue;
			} elseif( $k == 'image_file' ) {
                if( $data['image_type'] == 'nil' ) {
                    // remove the file
                    $opp->campaign_image_file_name = '';
                    $opp->campaign_image_content_type = '';
                    $opp->campaign_image_file_size = 0;
                    $opp->campaign_image_updated_at = date('Y-m-d H-i-s');
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

				$opp->set_file($temp_file, $temp_file_type);

				continue;
			} elseif( $k == 'youtube_url' || $k == 'youtube_id' ) {
				$k = 'youtube';
			} elseif( $k == 'donation_target' ) {
			    $k = 'target';
			}

			$opp->$k = $this->security->xss_clean($v);
		}

		if( array_key_exists('youtube', $data)
			&& strpos($data['youtube'], 'v=') !== false ) {
			$vars = array();
			$url = parse_url($data['youtube']);
			parse_str($url['query'], $vars);

			$opp->youtube = $vars['v'];
		} elseif( array_key_exists('youtube', $data)
			&& strpos($data['youtube'], 'youtu.be') !== false ) {

			$opp->youtube = str_replace('http://youtu.be/', '', $data['youtube']);
		}

		if( array_key_exists('campaign_responses', $data) ) {
			$opp->set_campaign_responses($data['campaign_responses']);
		}

		if( strpos($opp->youtube, 'http://') === 0 ) {
			$opp->youtube = false;
		}

		return $opp;
	}

}
