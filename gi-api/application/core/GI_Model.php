<?php

/**
 * The elder statesman of the CI extensions, GI_Model extends
 * the core Model library
 *
 * @class GI_Model
 * @extends  CI_Model
 */
class GI_Model extends CI_Model {

	protected $_stack = array();
	protected $_table = false;
	protected $_primary = 'id';

	public function __construct() {
		parent::__construct();

		$this->load->helper('inflector', 'api');
	}

	/**
	 * Assign propertys of one object to a new copy
	 * if a model
	 * @param  Object $obj
	 * @return Object
	 */
	public function assign($obj) {
		$mdl = new $this;

		foreach(get_object_vars($obj) as $k => $v) {
			$mdl->$k = $v;
		}

		return $mdl;
	}

	/**
	 * Determine object's public properties and return as array
	 * @return Array
	 */
	public function get_public_properties() {
		$v = create_function('', '$o = new '.get_class($this).'; return get_object_vars($o);');

		return array_keys($v());
	}

	/**
	 * Store value in internal stack
	 * @param  String $k Key
	 * @param  Mixed $v Value
	 */
	protected function store_value($k, $v) {
		$this->_stack[$k] = $v;
	}

	/**
	 * Get value from internal stack
	 * @param  String $k Key
	 * @return Mixed
	 */
	protected function retrieve_value($k) {
		if( !isset($this->_stack[$k]) ) {
			return false;
		}

		return $this->_stack[$k];
	}

	/**
	 * Attempt to determine active record table name for this model
	 * @return String
	 */
	protected function get_ar_table_name() {

		if( $this->_table ) {
			return $this->_table;
		}

		$class = get_class($this);
		$name =  strtolower(substr($class, 0, strrpos($class, '_')));

		return plural($name);
	}

	/**
	 * Save entry helper. Returns this model object
	 * @return Object
	 */
	public function save_entry() {
		$table = $this->get_ar_table_name();
		$key = $this->_primary;

		$tmp = new stdClass;
		foreach( $this->get_public_properties() as $p ) {
			$tmp->$p = $this->$p;
		}

		if( !$this->$key ) {
			$this->db->insert($table, $tmp);

			$this->$key = $this->db->insert_id();
		} else {
			$this->db->update(
				$table, $tmp, array($key => $this->$key)
			);
		}


		return $this;
	}

	/**
	 * Delete entry
	 * @param  Int $uid
	 * @return Object      this model
	 */
	public function delete_entry($uid) {
		$table = $this->get_ar_table_name();
		$key = $this->_primary;

		$query = $this->db->where($key, $uid)
			->delete($table);

		return $this;
	}

	/**
	 * Get single entry
	 * @param  Int $uid
	 * @return Object      This model
	 */
	public function get_entry($uid) {
		$table = $this->get_ar_table_name();
		$key = $this->_primary;

		$query = $this
			->where($table.'.'.$key, $uid)
			->find();

		return $query[0];
	}

	/**
	 * Flatten an array of models into a single key value array
	 *
	 * @param  Object  $obj
	 * @param  String  $prop
	 * @param  boolean $key
	 * @return Array
	 */
	public function flatten($obj, $prop, $key=false) {
		$this->load->helper('model');
		return flatten_model($obj, $prop, $key);
	}

	/**
	 * Normalize a string by removing everything by alnums, dot and underscore
	 *
	 * @param  String $str
	 * @return String
	 */
	public function normalize($str) {
		return strtolower(
			preg_replace(
				array('/[^a-zA-Z0-9\._ ]/', '/\s{2,}/', '/\.{2,}/', '/ /'),
				array('', ' ', '.', '_'),
				trim($str)
			)
		);
	}

	/**
	 * Fire off query, automatically assigns return results to this model
	 * @param  String $tbl   Override table name OPTIONAL
	 * @param  String $klass Override assigned class OPTIONAL
	 * @return Array        of models
	 */
	public function find($tbl = null, $klass = null) {
		if( $tbl == null ) {
			$tbl = $this->get_ar_table_name();
		}
		if( $klass == null ) {
			$klass = get_class($this);
		}

		return $this
			->get($tbl)
			->result($klass);
	}

	public function __call($f, $args = array()) {

		if( $f == 'get' ) {
			return call_user_func_array(
				array($this->db, $f),
				$args
			);
		}
		if( is_callable(array($this->db, $f)) ) {
			call_user_func_array(
				array($this->db, $f),
				$args
			);

			return $this;
		}

		if( method_exists($this, '__'.$f) ) {
			// special overloaded method
			return call_user_func_array(
				array($this, '__'.$f),
				$args
			);
		}

		// future proofing, may become necessary with CI updates
//		return parent::__call($f, $args);
	}

	public function __get($f) {

		if( method_exists($this, '__'.$f) ) {
			return call_user_func(array($this, '__'.$f));
		}

		return parent::__get($f);
	}

}