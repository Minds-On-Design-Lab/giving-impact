<?php
/**
 * Translate URL parameters into limit/sort options for API queries
 * @param  Object $obj A campaign, opportunity or donation model
 * @return Object
 */
function api_limit_sort($obj) {
	$CI = get_instance();

	$item = false;
	switch(get_class($obj)) {
		case 'Campaign_model':
		case 'Opportunity_model':
			$item = 'campaigns';
			break;
		case 'Donation_model':
			$item = 'donations';
			break;
        case 'Supporter_model':
            $item = 'supporters';
	}

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

	if( $CI->input->sort_name($item) == 'random' ) {
		$obj->order_by("rand()");
	} elseif( $CI->input->sort_name($item) && $CI->input->sort_dir() ) {
		$obj->order_by(sprintf(
			'%s.%s %s', $item, $CI->input->sort_name($item), $CI->input->sort_dir()
		));
	} elseif( $CI->input->sort_name() ) {
		$obj->order_by(sprintf(
			'%s.%s asc', $item, $CI->input->sort_name($item)
		));
	}

	if( $item == 'campaigns' ) {
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

/**
 * Generates alpha-numeric tokens that are guaranteed to have alphas and numbers
 * @return string
 */
function api_generate_token() {
    $tok = substr(sha1(uniqid().time().rand(0,99)), 0, 10);

    if( !preg_match('/[^0-9]/', $tok) ) {
        $alpha = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $letter = $alpha{(rand(0,strlen($alpha)-1))};

        $split = rand(0, strlen($tok)-2);

        $tok = sprintf(
            '%s%s%s',
            substr($tok, 0, $split),
            $letter,
            substr($tok, $split+1)
        );
    }

    return $tok;
}

