<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Admin controller
 *
 * @class Admin
 * @extends  CI_controller
 * @copyright  2014 Minds On Design Lab
 * @author  Mike Joseph <mikej@mod-lab.com>
 */
class Admin extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->library('auth');
        $this->load->library('giving_impact');
        $this->load->library('form_validation');

        $this->load->model('account_model');
        $this->load->model('user_model');

        $this->load->helper('notification');

        if( !$this->auth->is_authorized() || !$this->auth->authorized_user()->is_super_admin ) {
            $this->auth->handle_expired();
            return;
        }

    }

    /**
     * Main action
     *
     * Calls 'admin/index' with:
     *  * user User_model
     *
     */
	public function index() {
	    $user = $this->user_model
	        ->where('id', $this->session->userdata('user_id'))
	        ->find();

        $hooks = $this->db
            ->select('hooks.*, accounts.account_name')
            ->order_by('event')
            ->join('accounts', 'accounts.id = hooks.account_id', 'left')
            ->get('hooks')
            ->result();

	    $this->load->view('admin/index', array(
	        'user'      => $user[0],
            'hooks'     => $hooks
	    ));
	}

    public function hook() {
        $user = $this->user_model
            ->where('id', $this->session->userdata('user_id'))
            ->find();

        $hook = $this->db
            ->where('id', $this->uri->segment(3))
            ->get('hooks')
            ->result();

        $tasks = $this->db
            ->where('hook_id', $hook[0]->id)
            ->get('tasks')
            ->result();

        $this->load->view('admin/hook', array(
            'user'      => $user[0],
            'hook'      => $hook[0],
            'tasks'     => $tasks
        ));
    }

    public function update_hook() {

        $hook = $this->db
            ->where('id', $this->uri->segment(3))
            ->get('hooks')
            ->result();

        $status = $hook[0]->status ? 0 : 1;

        $this->db
            ->where('id', $this->uri->segment(3))
            ->limit(1)
            ->update('hooks', array('status' => $status));


        $this->session->set_flashdata('message', 'Hook updated');
        redirect(site_url('admin'));
    }

    public function clear_hook() {

        $hook = $this->db
            ->where('id', $this->uri->segment(3))
            ->get('hooks')
            ->result();

        $this->db
            ->where('hook_id', $hook[0]->id)
            ->delete('tasks');


        $this->session->set_flashdata('message', 'Hook updated');
        redirect(site_url('admin'));
    }

}
