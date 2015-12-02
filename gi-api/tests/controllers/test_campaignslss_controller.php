<?php

require_once APP_DIR.'/controllers/v2/campaigns.php';

class test_campaignslss_controller extends CodeIgniterUnitTestCase
{
	public $c = false;
	public $title = false;

	public function __construct() {

		parent::__construct('Campaign Controller LSS');

		$this->load->helper('test');
	}

	public function setUp()	{
		$this->c = new Campaigns;
		$this->title = 'Trash Me Tester '.rand(0,99999);
    }

    public function tearDown() {
		unset($this->c);
		$this->db->where('title', $this->title)
			->delete('campaigns');
		$this->db->where('last_name', $this->title)
			->delete('donations');
    }

	public function test_index() {
		set_valid_authorization();

		$_GET = array(
			'limit' => 1
		);

		$out = run_action($this->c, 'index');

		$json = json_decode($out, true);

		$this->assertTrue(is_array($json));
		$this->assertFalse($json['error']);
		$this->assertTrue(array_key_exists('campaigns', $json));
		$this->assertEqual(1, count($json['campaigns']));
	}

	public function test_index_order_up() {
		set_valid_authorization();

		$row = $this->db
			->where('single_access_token', '1234567890')
			->get('users')
			->result();

		$this->db->insert('campaigns', array(
			'account_id' => $row[0]->account_id,
//			'created_at' => date('Y-m-d g:i:s'),
			'created_at' => '1900-01-01 00:00:00',
			'title' => $this->title,
			'status' => 1
		));

		$_GET = array(
			'limit' => 1,
			'sort' => 'created_at'
		);

		$out = run_action($this->c, 'index');

		$json = json_decode($out, true);

		$this->assertTrue(is_array($json));
		$this->assertFalse($json['error']);
		$this->assertTrue(array_key_exists('campaigns', $json));
		$this->assertEqual(1, count($json['campaigns']));
		$this->assertEqual($this->title, $json['campaigns'][0]['title']);
	}

	public function test_index_order_down() {
		set_valid_authorization();

		$row = $this->db
			->where('single_access_token', '1234567890')
			->get('users')
			->result();

		$this->db->insert('campaigns', array(
			'account_id' => $row[0]->account_id,
			'created_at' => date('Y-m-d g:i:s'),
			'title' => $this->title,
			'status' => 1
		));

		$_GET = array(
			'limit' => 1,
			'sort' => 'created_at|desc'
		);

		$out = run_action($this->c, 'index');

		$json = json_decode($out, true);

		$this->assertTrue(is_array($json));
		$this->assertFalse($json['error']);
		$this->assertTrue(array_key_exists('campaigns', $json));
		$this->assertEqual(1, count($json['campaigns']));
		$this->assertEqual($this->title, $json['campaigns'][0]['title']);
	}

	public function test_index_status_active() {
		set_valid_authorization();

		$row = $this->db
			->where('single_access_token', '1234567890')
			->get('users')
			->result();

		$this->db->insert('campaigns', array(
			'account_id' => $row[0]->account_id,
			'created_at' => date('Y-m-d g:i:s'),
			'title' => $this->title,
			'status' => 1
		));

		$_GET = array(
			'status' => 'active'
		);

		$out = run_action($this->c, 'index');

		$json = json_decode($out, true);

		$this->assertTrue(is_array($json));
		$this->assertFalse($json['error']);
		$this->assertTrue(array_key_exists('campaigns', $json));

		$tester = array();
		foreach( $json['campaigns'] as $cmp ) {
			$tester[] = $cmp['title'];
		}

		$this->assertTrue(in_array($this->title, $tester));
	}

	public function test_index_status_active2() {
		set_valid_authorization();

		$row = $this->db
			->where('single_access_token', '1234567890')
			->get('users')
			->result();

		$this->db->insert('campaigns', array(
			'account_id' => $row[0]->account_id,
			'created_at' => date('Y-m-d g:i:s'),
			'title' => $this->title,
			'status' => 1
		));

		$_GET = array(
			'status' => 'inactive'
		);

		$out = run_action($this->c, 'index');

		$json = json_decode($out, true);

		$this->assertTrue(is_array($json));
		$this->assertFalse($json['error']);
		$this->assertTrue(array_key_exists('campaigns', $json));

		$tester = array();
		foreach( $json['campaigns'] as $cmp ) {
			$tester[] = $cmp['title'];
		}

		$this->assertFalse(in_array($this->title, $tester));
	}

	public function test_index_status_inactive() {
		set_valid_authorization();

		$row = $this->db
			->where('single_access_token', '1234567890')
			->get('users')
			->result();

		$this->db->insert('campaigns', array(
			'account_id' => $row[0]->account_id,
			'created_at' => date('Y-m-d g:i:s'),
			'title' => $this->title,
			'status' => 0
		));

		$_GET = array(
			'status' => 'inactive'
		);

		$out = run_action($this->c, 'index');

		$json = json_decode($out, true);

		$this->assertTrue(is_array($json));
		$this->assertFalse($json['error']);
		$this->assertTrue(array_key_exists('campaigns', $json));

		$tester = array();
		foreach( $json['campaigns'] as $cmp ) {
			$tester[] = $cmp['title'];
		}

		$this->assertTrue(in_array($this->title, $tester));
	}

	public function test_index_status_inactive2() {
		set_valid_authorization();

		$row = $this->db
			->where('single_access_token', '1234567890')
			->get('users')
			->result();

		$this->db->insert('campaigns', array(
			'account_id' => $row[0]->account_id,
			'created_at' => date('Y-m-d g:i:s'),
			'title' => $this->title,
			'status' => 0
		));

		$_GET = array(
			'status' => 'active'
		);

		$out = run_action($this->c, 'index');

		$json = json_decode($out, true);

		$this->assertTrue(is_array($json));
		$this->assertFalse($json['error']);
		$this->assertTrue(array_key_exists('campaigns', $json));

		$tester = array();
		foreach( $json['campaigns'] as $cmp ) {
			$tester[] = $cmp['title'];
		}

		$this->assertFalse(in_array($this->title, $tester));
	}

	public function test_opportunities_limit() {
		set_valid_authorization();
		$this->uri->rsegments[3] = 'b6286e5aa2';

		$_GET = array(
			'limit' => 1
		);

		$out = run_action($this->c, 'opportunities');

		$json = json_decode($out, true);

		$this->assertTrue(is_array($json));
		$this->assertFalse($json['error']);
		$this->assertTrue(array_key_exists('opportunities', $json));
		$this->assertTrue(1, count($json['opportunities']));
	}

	public function test_opportunities_sort() {
		set_valid_authorization();
		$this->uri->rsegments[3] = 'b6286e5aa2';

		$row = $this->db
			->where('single_access_token', '1234567890')
			->get('users')
			->result();

		$row2 = $this->db
			->where('campaign_token', 'b6286e5aa2')
			->get('campaigns')
			->result();

		$this->db->insert('campaigns', array(
			'account_id' => $row[0]->account_id,
			'campaign_id' => $row2[0]->id,
			'created_at' => date('1900-01-01 00:00:00'),
			'title' => $this->title,
			'status' => 1
		));

		$_GET = array(
			'sort' => 'created_at'
		);

		$out = run_action($this->c, 'opportunities');

		$json = json_decode($out, true);

		$this->assertTrue(is_array($json));
		$this->assertFalse($json['error']);
		$this->assertTrue(array_key_exists('opportunities', $json));
		$this->assertEqual($this->title, $json['opportunities'][0]['title']);
	}

	public function test_opportunities_sort_desc() {
		set_valid_authorization();
		$this->uri->rsegments[3] = 'b6286e5aa2';

		$row = $this->db
			->where('single_access_token', '1234567890')
			->get('users')
			->result();

		$row2 = $this->db
			->where('campaign_token', 'b6286e5aa2')
			->get('campaigns')
			->result();

		$this->db->insert('campaigns', array(
			'account_id' => $row[0]->account_id,
			'campaign_id' => $row2[0]->id,
			'created_at' => date('Y-m-d g:i:s'),
			'title' => $this->title,
			'status' => 1
		));

		$_GET = array(
			'sort' => 'created_at|desc'
		);

		$out = run_action($this->c, 'opportunities');

		$json = json_decode($out, true);

		$this->assertTrue(is_array($json));
		$this->assertFalse($json['error']);
		$this->assertTrue(array_key_exists('opportunities', $json));
		$this->assertEqual($this->title, $json['opportunities'][0]['title']);
	}

	public function test_donations_sort() {
		set_valid_authorization();
		$this->uri->rsegments[3] = 'b6286e5aa2';

		$row = $this->db
			->where('single_access_token', '1234567890')
			->get('users')
			->result();

		$row2 = $this->db
			->where('campaign_token', 'b6286e5aa2')
			->get('campaigns')
			->result();

		$this->db->insert('donations', array(
			'account_id' => $row[0]->account_id,
			'campaign_id' => $row2[0]->id,
			'campaign_group_id' => $row2[0]->id,
			'created_at' => date('1900-01-01 00:00:00'),
			'donation_date' => date('1900-01-01 00:00:00'),
			'last_name' => $this->title,
			'complete' => 1
		));

		$_GET = array(
			'sort' => 'created_at'
		);

		$out = run_action($this->c, 'donations');

		$json = json_decode($out, true);

		$this->assertTrue(is_array($json));
		$this->assertFalse($json['error']);
		$this->assertTrue(array_key_exists('donations', $json));
		$this->assertEqual($this->title, $json['donations'][0]['last_name']);
	}

	public function test_donations_sort_desc() {
		set_valid_authorization();
		$this->uri->rsegments[3] = 'b6286e5aa2';

		$row = $this->db
			->where('single_access_token', '1234567890')
			->get('users')
			->result();

		$row2 = $this->db
			->where('campaign_token', 'b6286e5aa2')
			->get('campaigns')
			->result();

		$this->db->insert('donations', array(
			'account_id' => $row[0]->account_id,
			'campaign_id' => $row2[0]->id,
			'campaign_group_id' => $row2[0]->id,
			'created_at' => date('Y-m-d h:m:s'),
			'donation_date' => date('Y-m-d h:m:s'),
			'last_name' => $this->title,
			'complete' => 1
		));

		$_GET = array(
			'sort' => 'created_at|desc'
		);

		$out = run_action($this->c, 'donations');

		$json = json_decode($out, true);

		$this->assertTrue(is_array($json));
		$this->assertFalse($json['error']);
		$this->assertTrue(array_key_exists('donations', $json));
		$this->assertEqual($this->title, $json['donations'][0]['last_name']);
	}

	public function test_donations_not_complete() {
		set_valid_authorization();
		$this->uri->rsegments[3] = 'b6286e5aa2';

		$row = $this->db
			->where('single_access_token', '1234567890')
			->get('users')
			->result();

		$row2 = $this->db
			->where('campaign_token', 'b6286e5aa2')
			->get('campaigns')
			->result();

		$this->db->insert('donations', array(
			'account_id' => $row[0]->account_id,
			'campaign_id' => $row2[0]->id,
			'campaign_group_id' => $row2[0]->id,
			'created_at' => date('1900-01-01 00:00:00'),
			'donation_date' => date('1900-01-01 00:00:00'),
			'last_name' => $this->title
		));

		$_GET = array(
			'sort' => 'created_at'
		);

		$out = run_action($this->c, 'donations');

		$json = json_decode($out, true);

		$this->assertTrue(is_array($json));
		$this->assertFalse($json['error']);
		$this->assertTrue(array_key_exists('donations', $json));
		$this->assertFalse(($this->title == $json['donations'][0]['last_name']));
	}

}
