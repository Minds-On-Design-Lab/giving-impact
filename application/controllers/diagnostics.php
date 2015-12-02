<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Diagnostics controller
 *
 * @class Diagnostics
 * @extends CI_Controller
 */
class Diagnostics extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->library('auth');

        if( !$this->auth->is_authorized() ) {
            $this->auth->handle_expired();
            return;
        }
    }

    /**
     * Index handler. Loads 'diagnostics/index' view with:
     *
     *  *
     *
     */
	public function index() {
		$this->load->view('diagnostics/index', array(
		));
	}

}
