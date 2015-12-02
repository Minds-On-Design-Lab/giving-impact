<?php

require_once APP_DIR.'/controllers/v2/campaigns.php';

class test_campaigns_controller extends CodeIgniterUnitTestCase
{
	public $c = false;

	public function __construct() {

		parent::__construct('Campaign Controller');

		$this->load->helper('test');
	}

	public function setUp()	{
		$this->c = new Campaigns;
    }

    public function tearDown() {
		unset($this->c);
    }

	public function test_index() {
		set_valid_authorization();
		$out = run_action($this->c, 'index');

		$json = json_decode($out, true);

		$this->assertTrue(is_array($json));
		$this->assertFalse($json['error']);
		$this->assertTrue(array_key_exists('campaigns', $json));
		$this->assertTrue(count($json['campaigns']) >= 1);
	}

	public function test_index_properties() {
		set_valid_authorization();
		$out = run_action($this->c, 'index');

		$json = json_decode($out, true);

		$campaign = $json['campaigns'][0];

		$this->assertTrue(count($campaign));

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
			"has_giving_opportunities",
			"display_total",
			"display_current"
		);

		foreach( $required_keys as $k ) {
			$this->assertTrue(array_key_exists($k, $campaign));
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
			$this->assertIsA($campaign[$k], 'string');
		}

		$check_keys = array(
			"status",
			"has_giving_opportunities",
			"display_total",
			"display_current"
		);

		foreach( $check_keys as $k ) {
			$this->assertIsA($campaign[$k], 'bool');
		}

	}

	public function test_token() {
		$this->uri->rsegments[3] = 'b6286e5aa2';
		set_valid_authorization();
		$out = run_action($this->c, 'token');

		$json = json_decode($out, true);

		$this->assertTrue(is_array($json));

		$this->assertFalse($json['error']);
		$this->assertTrue(array_key_exists('campaign', $json));
		$this->assertFalse(array_key_exists('campaigns', $json));

	}

	public function test_token_properties() {

		$this->uri->rsegments[3] = 'b6286e5aa2';
		set_valid_authorization();
		$out = run_action($this->c, 'token');

		$json = json_decode($out, true);

		$campaign = $json['campaign'];

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
			"has_giving_opportunities",
			"display_total",
			"display_current"
		);

		foreach( $required_keys as $k ) {
			$this->assertTrue(array_key_exists($k, $campaign));
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
			$this->assertIsA($campaign[$k], 'string');
		}

		$check_keys = array(
			"status",
			"has_giving_opportunities",
			"display_total",
			"display_current"
		);

		foreach( $check_keys as $k ) {
			$this->assertIsA($campaign[$k], 'bool');
		}
	}

	public function test_token_invalid() {
		// make sure error returned for bad token
		$this->uri->rsegments[3] = 'b';
		set_valid_authorization();
		$out = run_action($this->c, 'token');

		$json = json_decode($out, true);

		$this->assertTrue($json['error']);
		$this->assertFalse(array_key_exists('campaign', $json));
		$this->assertFalse(array_key_exists('campaigns', $json));
	}

	public function test_token_opportunity() {
		// make sure error returned for valid opportunity token
		$this->uri->rsegments[3] = '084e80fca4';
		set_valid_authorization();
		$out = run_action($this->c, 'token');

		$json = json_decode($out, true);

		$this->assertTrue($json['error']);
		$this->assertFalse(array_key_exists('campaign', $json));
		$this->assertFalse(array_key_exists('campaigns', $json));
	}

	public function test_opportunities() {
		$this->uri->rsegments[3] = 'b6286e5aa2';
		set_valid_authorization();
		$out = run_action($this->c, 'opportunities');

		$json = json_decode($out, true);

		$this->assertFalse($json['error']);
		$this->assertTrue(array_key_exists('opportunities', $json));
		$this->assertTrue(count($json['opportunities']) >= 1);

	}

	public function test_opportunities_bad() {
		$this->uri->rsegments[3] = 'b';
		set_valid_authorization();
		$out = run_action($this->c, 'opportunities');

		$json = json_decode($out, true);

		$this->assertTrue($json['error']);
		$this->assertFalse(array_key_exists('opportunities', $json));

	}

	public function test_opportunities_properties() {
		$this->uri->rsegments[3] = 'b6286e5aa2';
		set_valid_authorization();
		$out = run_action($this->c, 'opportunities');

		$json = json_decode($out, true);

		$this->assertFalse($json['error']);
		$this->assertTrue(array_key_exists('opportunities', $json));
		$this->assertTrue(count($json['opportunities']) >= 1);

		$opportunity = $json['opportunities'][0];

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

	public function test_donations() {
		$this->uri->rsegments[3] = 'b6286e5aa2';
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
		$this->uri->rsegments[3] = 'b6286e5aa2';
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

		$check_keys = array(
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
			"created_at"
		);

		foreach( $check_keys as $k ) {
			$this->assertIsA($donation[$k], 'string');
		}

		$check_keys = array(
			"offline",
			"twitter_share",
			"fb_share"
		);

		foreach( $check_keys as $k ) {
			$this->assertIsA($donation[$k], 'bool');
		}

	}

}
