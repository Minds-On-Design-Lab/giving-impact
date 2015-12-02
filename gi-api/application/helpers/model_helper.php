<?php

/**
 * Automatically add limits and sorts to database queries based on
 * provided search parameters
 * @param  Object $obj  MOD_Model
 * @param  String $item 'campaign', 'opportunity', or 'donation'
 * @return Object       MOD_Model
 */
function limit_sorterize($obj, $item) {
	$CI = get_instance();

	if( $CI->input->offset() && $CI->input->limit() ) {
		$obj->limit(
			$CI->input->limit(),
			$CI->input->offset()
		);
	} elseif( $CI->input->limit() ) {
		$obj->limit(
			$CI->input->limit()
		);
	}

	if( $CI->input->sort_name() == 'random' ) {
		$obj->order_by("rand()");
	} elseif( $CI->input->sort_name() && $CI->input->sort_dir() ) {
		$obj->order_by(sprintf(
			'%s.%s %s', $item, $CI->input->sort_name(), $CI->input->sort_dir()
		));
	} elseif( $CI->input->sort_name() ) {
		$obj->order_by(sprintf(
			'%s.%s', $item, $CI->input->sort_name()
		));
	}

	if( $item == 'campaigns' || $item == 'opportunities' ) {
		if( $CI->input->status() !== false ) {
			if( $CI->input->status() ) {
				$obj->where('status = 1');
			} else {
				$obj->where('status = 0');
			}
		}
	}

	return $obj;
}

