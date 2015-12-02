<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Checkout js controller
 *
 * @class Checkout
 * @extends CI_Controller
 */
class Checkout extends CI_Controller {


	public function __construct() {
		parent::__construct();

		$this->load->model('Campaign_model');
		$this->load->model('Opportunity_model');
		$this->load->model('User_model');
		$this->load->helper('api');
	}

	public function index() {
		$key = $this->input->get('key');

		if( !$key ) {
			$this->output->show_error('Public api key required', 400, 'badRequest');
			return;
		}

		$user = $this->User_model
			->where('public_access_token', $key)
			->find();

		if( !count($user) ) {
			$this->output->show_error('A key was provided, but is not valid.', 400, 'badRequest');
			return;
		}

		$out = $this->load->view('v2/checkout/index.js', array('user' => $user[0]), true);

		header('Content-type: text/javascript');
		echo $out;
		return;
	}

}
