<?php

function set_valid_authorization() {
	$CI = get_instance();

	$CI->authorization->reload_auth('test-ua', '1234567890');
}

function set_invalid_authorization() {
	$CI = get_instance();

	$CI->authorization->reload_auth('test-ua', '0');
}

function run_action($controller, $action, $args = array()) {
	ob_start();
	call_user_func_array(
		array($controller, $action),
		$args
	);
	$out = ob_get_contents();
	ob_end_clean();

	return $out;
}