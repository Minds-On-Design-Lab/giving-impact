<?php

/**
 * Extends core Loader class
 *
 * @class GI_Loader
 * @extends CI_loader
 */
class GI_Loader extends CI_Loader {

	/**
	 * Override main view callback for API. Automatically appends
	 * request type to template path (either 'json' or 'xml')
	 * @param  String  $view   Base view path
	 * @param  array   $vars   Vars to pass to template
	 * @param  boolean $return If 'TRUE' return the template
	 * @return Mixed
	 */
	public function view($view, $vars = array(), $return = FALSE) {

		$format = get_instance()->uri->output_format();
		if( !$format ) {
			return parent::view($view, $vars, $return);
		}

		$check_path = sprintf('%sviews/%s.%s', APPPATH, $view, $format);
		if( !file_exists($check_path) ) {
			return parent::view($view, $vars, $return);
		}

		return parent::view(
			sprintf('%s.%s', $view, $format),
			$vars,
			$return
		);
	}

}