<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Stats controller
 *
 * @class stats
 * @extends  CI_Controller
 */
class Stats extends CI_Controller {

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
     */
	public function index() {

		$this->load->view('v2/stats/index');
	}

    /**
     * Range handler. Handles:
     *
     *  * GET /api/v2/stats/range?before={string}&after={string}
     *
     * Accepts via GET:
     *
     *  * after {String} timestamp
     *  * before {String} timestamp
     *
     * Accepts the opportunity or campaign hash as 3rd URL segment
     *
     * @return Array of Stat_model objects
     */
	public function range() {
        $after = $this->input->get('after');
        $before = $this->input->get('before');

        if( !$after ) {
            $after = strtotime('01/01/2000 00:00:00');
        } else {
            $after = strtotime($after.' 00:00:00');
        }
        if( !$before ) {
            $before = strtotime('+1 day 00:00:00');
        } else {
            $before = strtotime($before.' 00:00:00');
        }

        $after = date('Y-m-d H:i:s', $after);
        $before = date('Y-m-d H:i:s', $before);

        $segments = $this->uri->segment_array();
        $tok = array_shift(array_slice($segments, -3));

        $type = 'campaign';
        if( strpos($this->uri->uri_string(), 'campaigns') !== false ) {
            $campaigns = $this->Campaign_model
                ->where('campaigns.account_id', $this->authorization->account)
                ->where('campaigns.campaign_token', $tok)
                ->where('campaigns.campaign_id', NULL)
                ->find();
        } else {
            $type = 'opportunity';
            $campaigns = $this->Opportunity_model
                ->where('campaigns.account_id', $this->authorization->account)
                ->where('campaigns.campaign_token', $tok)
                ->find();
        }

		if( !count($campaigns) ) {
			$this->log->entry('Campaign not found for token '.$tok);
			$this->output->show_error('Campaign not found', 404, 'invalid_token');
			return;
		}

        $donations = $this->db
            ->select('COUNT(id) as total, DATE(created_at) as date, SUM(amount) as amount')
            ->where(sprintf('created_at between "%s" and "%s"', $after, $before));
        if( $type == 'campaign' ) {
            $donations->where('campaign_group_id', $campaigns[0]->id);
        } else {
            $donations->where('campaign_id', $campaigns[0]->id);
        }
        $donations = $donations
            ->where('complete = 1')
            ->where('refunded = 0')
            ->group_by('date')
            ->order_by('created_at desc')
            ->get('donations')
            ->result();

        $out = array();

        $default = array(
            'total_donations'    => 0,
            'donation_total'    => 0,
            'date'              => false
        );

        // For reference:
        // select DATE(created_at) as date, SUM(amount) from donations group by date order by created_at desc limit 10;
        $tz = $this->authorization->get_account()->timezone;
        $newTimeZone = new DateTimeZone($tz);

        foreach( $donations as $donation ) {
            $k = new DateTime(date('c', strtotime($donation->date)));
            $k = $k->setTimeZone($newTimeZone)->format('c');

            if( !array_key_exists($k, $out) ) {
                $out[$k] = $default;
                $out[$k]['date'] = $k;
            }

            $out[$k]['total_donations'] = $donation->total;
            $out[$k]['donation_total'] = $donation->amount;
        }

        $this->load->view('v2/stats/index', array(
            'stats' => array_values($out)
        ));

	}

    /**
     * Log handler.
     *
     * Handles:
     *
     *  * GET /api/v2/stats/log?limit={int}&offset={int}
     *
     * Accepts via GET:
     *
     *  * limit {Int}
     *  * offset {Int}
     *
     * Accepts campaign or opportunity id in 3rd URL segment
     *
     * @return Array of Stat_model objects
     */
	public function log() {
        $limit = $this->input->limit() ? $this->input->limit() : 10;
        $offset = $this->input->offset() ? $this->input->offset() : 0;

        $segments = $this->uri->segment_array();
        $tok = array_shift(array_slice($segments, -3));

        $type = 'campaign';
        if( strpos($this->uri->uri_string(), 'campaigns') !== false ) {
            $campaigns = $this->Campaign_model
                ->where('campaigns.account_id', $this->authorization->account)
                ->where('campaigns.campaign_token', $tok)
                ->where('campaigns.campaign_id', NULL)
                ->find();
        } else {
            $type = 'opportunity';
            $campaigns = $this->Opportunity_model
                ->where('campaigns.account_id', $this->authorization->account)
                ->where('campaigns.campaign_token', $tok)
                ->find();
        }

		if( !count($campaigns) ) {
			$this->log->entry('Campaign not found for token '.$tok);
			$this->output->show_error('Campaign not found', 404, 'invalid_token');
			return;
		}

        $donations = $this->db
            ->select('COUNT(id) as total, DATE(created_at) as date, SUM(amount) as amount');
        if( $type == 'campaign' ) {
            $donations->where('campaign_group_id', $campaigns[0]->id);
        } else {
            $donations->where('campaign_id', $campaigns[0]->id);
        }
        $donations = $donations
            ->where('complete = 1')
            ->where('refunded = 0')
            ->group_by('date')
            ->order_by('created_at desc')
            ->limit(($limit*2), $offset)
            ->get('donations')
            ->result();

        $out = array();

        $default = array(
            'total_donations'    => 0,
            'donation_total'    => 0,
            'date'              => false
        );

        $tz = $this->authorization->get_account()->timezone;
        $newTimeZone = new DateTimeZone($tz);

        // For reference:
        // select DATE(created_at) as date, SUM(amount) from donations group by date order by created_at desc limit 10;
        foreach( $donations as $donation ) {
            // $k = $donation->date;
            $k = new DateTime(date('c', strtotime($donation->date)));
            $k = $k->setTimeZone($newTimeZone)->format('c');

            if( !array_key_exists($k, $out) ) {
                $out[$k] = $default;
                $out[$k]['date'] = $k;
            }

            $out[$k]['total_donations'] = $donation->total;
            $out[$k]['donation_total'] = $donation->amount;
        }

        $out = array_slice(array_values($out), 0, $limit);

        $this->load->view('v2/stats/index', array(
            'stats' => $out
        ));

	}

}