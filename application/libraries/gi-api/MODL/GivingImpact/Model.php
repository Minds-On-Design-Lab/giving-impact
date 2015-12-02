<?php

namespace MODL\GivingImpact;

/**
 * Model base, provides the base for the fluent interface
 *
 * @class Model
 * @namespace  MODL\GivingImpact
 */
class Model {

	/**
	 * Dependency Injector
	 * @var boolean
	 */
	protected $container = false;

	/**
	 * Possible properties that can be used to agument API returns
	 * @var array
	 */
	protected $properties = array(
		'related' => false,
		'limit' => false,
		'offset' => false,
		'sort' => 'created_at|desc',
		'status' => false,
		'supporter' => false
	);

	/**
	 * Return an array of public properties
	 * @return Array
	 */
	public function publicProperties() {
		$v = create_function('', '$o = new '.get_class($this).'(null); return get_object_vars($o);');

		return array_keys($v());
	}

	/**
	 * Assign one object's properties into a new object model
	 * @param  Object $obj
	 * @return Object
	 */
	public function assign($obj) {
		foreach(get_object_vars($obj) as $k => $v) {
			$this->$k = $v;
		}

		return $this;
	}

	/**
	 * Base fetch class, overridden by most models
	 * @param  String $token
	 * @return Object
	 */
	public function fetch($token) {
		$rc = $this->container->restClient;
		$rc->url = $rc->url.'/'.$this->path.'/'.$token;

		return $rc->get($this->properties);
	}

	/**
	 * Set the related property
	 *
	 * 	<pre>
	 * 		$foo->related('campaign');
	 * 	</pre>
	 *
	 * @param  String $related
	 * @return Object          this
	 */
	public function related($related) {
		$this->properties['related'] = $related;

		return $this;
	}

	/**
	 * Set limit property. Usually used in conjunction with offset
	 *
	 * 	<pre>
	 * 		$foo
	 * 			->offset(20)
	 * 			->limit(10);
	 * 	</pre>
	 *
	 * @param  Int $lim
	 * @return Object   	this
	 */
	public function limit($lim) {
		$this->properties['limit'] = $lim;

		return $this;
	}

	/**
	 * Set offset property. Usually used on conjunction with limit
	 *
	 * 	<pre>
	 * 		$foo
	 * 			->limit(10)
	 * 			->offset(2);
	 * 	</pre>
	 *
	 * @param  Int $os
	 * @return Object     this
	 */
	public function offset($os) {
		$this->properties['offset'] = $os;

		return $this;
	}

	/**
	 * Set sort property. Direction cna be added with a pipe
	 *
	 * 	<pre>
	 * 		$foo->sort('name');
	 * 		$foo->sort('name|desc');
	 * 	</pre>
	 *
	 * @param  String $order String
	 * @return Object this
	 */
	public function sort($order) {
		$this->properties['sort'] = $order;

		return $this;
	}

	/**
	 * Status property
	 *
	 * 	<pre>
	 * 		$foo->status('active');
	 * 	</pre>
	 *
	 * @param  String $status can be either 'active' or 'inactive'
	 * @return Object 	this
	 */
	public function status($status) {
		$this->properties['status'] = $status;

		return $this;
	}

	/**
	 * Set supporter property for filtering
	 *
	 *	<pre>
	 *		$foo->supporter('test@mod-lab.com');
	 *  </pre>
	 *
	 * @param  String $id Either a supporter token or email
	 * @return Object this
	 */
	public function supporter($id) {
		$this->properties['supporter'] = $id;

		return $this;
	}
}
