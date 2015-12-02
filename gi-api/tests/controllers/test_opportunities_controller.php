<?php
error_reporting(E_ALL ^ E_NOTICE);
require_once APP_DIR.'/controllers/v2/opportunities.php';

class test_opportunities_controller extends CodeIgniterUnitTestCase
{
	public $c = false;

	public function __construct() {

		parent::__construct('Opportunity Controller');

		$this->load->helper('test');
	}

	public function setUp()	{
		$this->c = new Opportunities;
    }

    public function tearDown() {
    	$this->input->post_body = false;
		$this->uri->rsegments[3] = false;
		unset($this->c);
    }

	public function test_token() {
		$this->uri->rsegments[3] = '084e80fca4';
		set_valid_authorization();
		$out = run_action($this->c, 'token');

		$json = json_decode($out, true);

		$this->assertTrue(is_array($json));

		$this->assertFalse($json['error']);
		$this->assertTrue(array_key_exists('opportunity', $json));

	}

	public function test_token_properties() {
		$this->uri->rsegments[3] = '084e80fca4';
		set_valid_authorization();
		$out = run_action($this->c, 'token');

		$json = json_decode($out, true);

		$this->assertFalse($json['error']);
		$this->assertTrue(array_key_exists('opportunity', $json));

		$opportunity = $json['opportunity'];

		$this->assertTrue(count($opportunity));

		$required_keys = array(
			"id_token",
			"title",
			"description",
			"youtube_id",
			"donation_total",
			"donation_target",
			"givlink",
			"donation_url",
			"share_url",
			"shares_fb",
			"shares_twitter",
			"hash_tag",
			"status",
			"image_url"
		);

		foreach( $required_keys as $k ) {
			$this->assertTrue(array_key_exists($k, $opportunity));
		}

		$check_keys = array(
			"id_token",
			"title",
			"description",
			"youtube_id",
			"donation_total",
			"donation_target",
			"givlink",
			"donation_url",
			"share_url",
			"shares_fb",
			"shares_twitter",
			"hash_tag"
		);

		foreach( $check_keys as $k ) {
			$this->assertIsA($opportunity[$k], 'string');
		}

		$check_keys = array(
			"status"
		);

		foreach( $check_keys as $k ) {
			$this->assertIsA($opportunity[$k], 'bool');
		}

	}

	public function test_token_invalid() {
		// make sure error returned for bad token
		$this->uri->rsegments[3] = 'b';
		set_valid_authorization();
		$out = run_action($this->c, 'token');

		$json = json_decode($out, true);

		$this->assertTrue($json['error']);
		$this->assertFalse(array_key_exists('opportunity', $json));
		$this->assertFalse(array_key_exists('opportunities', $json));
	}

	public function test_token_opportunity() {
		// make sure error returned for valid opportunity token
		$this->uri->rsegments[3] = 'b6286e5aa2';
		set_valid_authorization();
		$out = run_action($this->c, 'token');

		$json = json_decode($out, true);

		$this->assertTrue($json['error']);
		$this->assertFalse(array_key_exists('opportunity', $json));
		$this->assertFalse(array_key_exists('opportunities', $json));
	}

	public function test_token_post_properties() {
		$this->uri->rsegments[3] = '084e80fca4';
		set_valid_authorization();

		$title = 'Test Title '.rand(1,9999);

		$this->input->post_body = new stdClass;
		$this->input->post_body->title = $title;
		$this->input->post_body->description = 'test';
		$this->input->post_body->status = 1;

		$out = run_action($this->c, 'token');

		$json = json_decode($out, true);

		$this->assertFalse($json['error']);
		$this->assertTrue(array_key_exists('opportunity', $json));

		$opportunity = $json['opportunity'];

		$this->assertTrue(count($opportunity));
		$this->assertEqual($title, $opportunity['title']);
	}

	public function test_token_post_properties_incomplete() {
		$this->uri->rsegments[3] = '084e80fca4';
		set_valid_authorization();

		$title = 'Test Title '.rand(1,9999);

		$this->input->post_body = new stdClass;
		$this->input->post_body->title = $title;

		$out = run_action($this->c, 'token');

		$json = json_decode($out, true);

		$this->assertTrue($json['error']);
		$this->assertFalse(array_key_exists('opportunity', $json));
	}

	public function test_post_properties() {
		set_valid_authorization();

		$title = 'Test Title '.rand(1,9999);

		$this->input->post_body = new stdClass;
		$this->input->post_body->title = $title;
		$this->input->post_body->description = 'test';
		$this->input->post_body->status = 1;
		$this->input->post_body->campaign_token = 'b6286e5aa2';

		$out = run_action($this->c, 'index');

		$this->db->where('title', $title)
			->delete('campaigns');

		$json = json_decode($out, true);

		$this->assertFalse($json['error']);
		$this->assertTrue(array_key_exists('opportunity', $json));

		$opportunity = $json['opportunity'];

		$this->assertTrue(count($opportunity));
		$this->assertEqual($title, $opportunity['title']);
	}

	public function test_post_properties_incomplete() {
		set_valid_authorization();

		$title = 'Test Title '.rand(1,9999);

		$this->input->post_body = new stdClass;
		$this->input->post_body->title = $title;

		$out = run_action($this->c, 'token');

		$json = json_decode($out, true);

		$this->assertTrue($json['error']);
		$this->assertFalse(array_key_exists('opportunity', $json));
	}

	public function test_donations() {
		$this->uri->rsegments[3] = '084e80fca4';
		set_valid_authorization();
		$out = run_action($this->c, 'donations');

		$json = json_decode($out, true);

		$this->assertFalse($json['error']);
		$this->assertTrue(array_key_exists('donations', $json));
		$this->assertTrue(count($json['donations']) >= 1);

	}

	public function test_donations_bad() {
		$this->uri->rsegments[3] = 'b';
		set_valid_authorization();
		$out = run_action($this->c, 'donations');

		$json = json_decode($out, true);

		$this->assertTrue($json['error']);
		$this->assertFalse(array_key_exists('donations', $json));

	}

	public function test_donations_properties() {
		$this->uri->rsegments[3] = '084e80fca4';
		set_valid_authorization();
		$out = run_action($this->c, 'donations');

		$json = json_decode($out, true);

		$this->assertFalse($json['error']);
		$this->assertTrue(array_key_exists('donations', $json));
		$this->assertTrue(count($json['donations']) >= 1);

		$donation = $json['donations'][0];

		$this->assertTrue(count($donation));

		$required_keys = array(
			"first_name",
			"last_name",
			"billing_address1",
			"billing_city",
			"billing_state",
			"billing_postal_code",
			"billing_country",
			"donation_total",
			"individual_total",
			"donation_level",
			"email_address",
			"referrer",
			"offline",
			"created_at",
			"twitter_share",
			"fb_share"
		);

		foreach( $required_keys as $k ) {
			$this->assertTrue(array_key_exists($k, $donation));
		}

	}
}
