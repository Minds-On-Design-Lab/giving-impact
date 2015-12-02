<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Link model
 *
 * @class Link_model
 * @extends  GI_Model
 */
class Link_model extends GI_Model {

    public $id                  = false;
    public $website_url         = false;
    public $short_token         = false;
    public $short_url           = false;
    public $campaign_id         = false;
    public $created_at          = false;
    public $updated_at          = false;

	public function __construct() {
		parent::__construct();

	}
}
