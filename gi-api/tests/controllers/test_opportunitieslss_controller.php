<?php
error_reporting(E_ALL ^ E_NOTICE);

require_once APP_DIR.'/controllers/v2/opportunities.php';

class test_opportunitieslss_controller extends CodeIgniterUnitTestCase
{
	public $c = false;
	public $title = false;

	public function __construct() {

		parent::__construct('Opportunity Controller LSS');

		$this->load->helper('test');
	}

	public function setUp()	{
		$this->c = new Opportunities;
		$this->title = 'Trash Me Tester '.rand(0,99999);
    }

    public function tearDown() {
    	$this->input->post_body = false;
		$this->uri->rsegments[3] = false;

		$this->db->where('last_name', $this->title)
			->delete('donations');

		unset($this->c);
    }

	public function test_donations_sort() {
		set_valid_authorization();
		$this->uri->rsegments[3] = '084e80fca4';

		$row = $this->db
			->where('single_access_token', '1234567890')
			->get('users')
			->result();

		$row2 = $this->db
			->where('campaign_token', '084e80fca4')
			->get('campaigns')
			->result();

		$this->db->insert('donations', array(
			'account_id' => $row[0]->account_id,
			'campaign_id' => $row2[0]->id,
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
		$this->uri->rsegments[3] = '084e80fca4';

		$row = $this->db
			->where('single_access_token', '1234567890')
			->get('users')
			->result();

		$row2 = $this->db
			->where('campaign_token', '084e80fca4')
			->get('campaigns')
			->result();

		$this->db->insert('donations', array(
			'account_id' => $row[0]->account_id,
			'campaign_id' => $row2[0]->id,
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
		$this->uri->rsegments[3] = '084e80fca4';

		$row = $this->db
			->where('single_access_token', '1234567890')
			->get('users')
			->result();

		$row2 = $this->db
			->where('campaign_token', '084e80fca4')
			->get('campaigns')
			->result();

		$this->db->insert('donations', array(
			'account_id' => $row[0]->account_id,
			'campaign_id' => $row2[0]->id,
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
