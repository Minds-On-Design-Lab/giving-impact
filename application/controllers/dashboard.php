<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dashboard controller
 *
 * @class Dashboard
 * @extends CI_Controller
 */
class Dashboard extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->library('auth');
        $this->load->library('giving_impact');

        $this->load->helper('number');

        if( !$this->auth->is_authorized() ) {
            $this->auth->handle_expired();
            return;
        }
    }

    /**
     * Index handler. Loads 'dashboard/index' view with:
     *
     *  * campaigns Array of Campaign_model
     *  * donations Array of Donation_model
     *
     */
	public function index() {
	    $campaigns = $this->giving_impact
	        ->campaign
	        ->status('active')
	        ->limit(1000)
	        ->fetch();

        $donations = $this->giving_impact
            ->donation
            ->limit(10)
            ->related(true)
            ->sort('donation_date|desc')
            ->fetch();

		$this->load->view('dashboard/index', array(
		    'campaigns' => $campaigns,
		    'donations' => $donations
		));
	}

    /**
     * View only inactive campaigns
     *
     * Loads 'dashboard/inactive' view with:
     *
     *  * inactive Array of Campaign_models
     *  * campaigns Array of Campaign_models
     * @return [type] [description]
     */
	public function inactive() {
	    $campaigns = $this->giving_impact
	        ->campaign
	        ->status('inactive')
	        ->limit(1000)
	        ->fetch();

	    $active_campaigns = $this->giving_impact
	        ->campaign
	        ->status('active')
	        ->limit(1000)
	        ->fetch();

	    $this->load->view('dashboard/inactive', array(
	        'inactive' => $campaigns,
	        'campaigns' => $active_campaigns
	    ));
	}
}
