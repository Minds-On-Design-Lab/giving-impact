<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Errors extends CI_Controller {

	public function missing() {
		$this->output->show_error('Requested resource does not exist.', 404, 'missing');
	}

}
