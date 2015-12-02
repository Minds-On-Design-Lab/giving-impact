<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Task model
 */
class Task_model extends GI_Model {

	public $id = false;
	public $event = false;
	public $hook_id = false;
	public $next_run = false;
	public $first_run = false;
	public $created = false;
	public $item = false;
	public $item_id = false;

	public function createTask($hook, $obj) {

		$task = new Task_model;

		$task->event 		= $hook->event;
		$task->hook_id		= $hook->id;
		$task->next_run		= 0;
		$task->first_run	= 0;
		$task->created 		= time();
		$task->item_id 		= $obj->id;

		return $task;

	}

}