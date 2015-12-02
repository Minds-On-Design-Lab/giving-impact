<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Hook model
 */
class Hook_model extends GI_Model {

	public $id = false;
	public $event = false;
	public $url = false;
	public $hash = false;
    public $status = false;
	public $last_run = false;
	public $account_id = false;
	public $uuid = false;

}