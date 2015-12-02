<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migrate extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->library('migration');

    }

	public function index() {
	}
}
