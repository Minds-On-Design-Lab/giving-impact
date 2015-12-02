<?php

/**
 * GI_model base
 *
 * @class GI_Model
 * @extends  GI_Model
 */
class GI_Model extends CI_Model {

	/**
	 * property stack
	 * @var array
	 */
	protected $_stack = array();

	/**
	 * Set table name override
	 * @var boolean
	 */
	protected $_table = false;

	/**
	 * Table primary key
	 * @var string
	 */
	protected $_primary = 'id';

	public function __construct() {
		parent::__construct();

		$this->load->helper('inflector', 'api');
	}

	/**
	 * Assign database return to object
	 * @param  Object $obj Result object
	 * @return Object      Model
	 */
	public function assign($obj) {
		$mdl = new $this;

		foreach(get_object_vars($obj) as $k => $v) {
			$mdl->$k = $v;
		}

		return $mdl;
	}

	/**
	 * Return array of public properties for model
	 * @return Array
	 */
	public function get_public_properties() {
		$v = create_function('', '$o = new '.get_class($this).'; return get_object_vars($o);');

		return array_keys($v());
	}

	/**
	 * Store value in the temporary stack
	 * @param  String $k
	 * @param  Mixed $v
	 */
	protected function store_value($k, $v) {
		$this->_stack[$k] = $v;
	}

	/**
	 * Retrieve value from stack
	 * @param  String $k
	 * @return Mixed
	 */
	protected function retrieve_value($k) {
		if( !isset($this->_stack[$k]) ) {
			return false;
		}

		return $this->_stack[$k];
	}

	/**
	 * Fetch active record table name, attempting to lower-case and pluralize
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
	 * Save handler. Will attempt to insert or update, depending
	 * on model values. Allows for fluent interface
	 *
	 * @return Object this
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
	 * Delete handler
	 * @param  Int $uid
	 * @return Object      this
	 */
	public function delete_entry($uid) {
		$table = $this->get_ar_table_name();
		$key = $this->_primary;

		$query = $this->db->where($key, $uid)
			->delete($table);

		return $this;
	}

	/**
	 * Fetch entry, automatically fetches based on table and ID
	 * @example
	 *  $foo = $this->Foo_model
	 *  		->get_entry(2);
	 *
	 * @param  Int $uid
	 * @return Object
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
	 * Flatten model collection into a key->value array
	 *
	 * @param Mixed $obj
	 * @param String $prop
	 * @param String $key
	 * @return Array
	 */
	public function flatten($obj, $prop, $key=false) {
		$this->load->helper('model');
		return flatten_model($obj, $prop, $key);
	}

	/**
	 * Normalize a string by removing anything by alnums, '.' and '_'
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
	 * Execute select query, return array of model objects. Automatically
	 * discovers table name, but can be overridden.
	 *
	 * @param  String $tbl OPTIONAL
	 * @param  String $klass OPTIONAL
	 * @return DB Result
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