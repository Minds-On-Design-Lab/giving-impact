<?php
/**
 * Prep campaigns and opportunities for output
 * @param  Array  $rows     Search results
 * @param  boolean $recurse
 * @return Array
 */
function prep_campaigns($rows, $recurse = true) {

	get_instance()->load->helper('url');

	$container_uri = false;
	$gi_url = get_instance()->config->item('gi_base_url');

	$out = array();
	$acct = get_instance()->authorization->get_account();
	$tz = $acct->timezone ? $acct->timezone : 'UTC';
	$currency = $acct->currency;

	foreach( $rows as $row ) {

		$newTimeZone = new DateTimeZone($tz);
		$date = new DateTime(date('c', strtotime($row->created_at)));
		$date->setTimeZone($newTimeZone);

		$item = array(
			"id_token"				=> $row->campaign_token,
			"status"				=> $row->status ? true : false,
			"title"					=> $row->title,
			// "created"				=> date('r', strtotime($row->created_at)),
			"created"				=> 	$date->format('c'),
			"description"			=> $row->description ? strip_tags($row->description, '<p><a><strong><b><i><em><br><ol><ul><li>') : "",
			"donation_url"			=> $gi_url.'initiate_donation/'.$row->campaign_token,
			"donation_target"		=> $row->target ? $row->target : "0"
		);

		if( !property_exists($row, 'campaign_id') || !$row->campaign_id ) {
	        $item["donation_minimum"]		= $row->minimum_donation_amount ? $row->minimum_donation_amount : "0";
	    }

		$item["donation_total"]			= $row->current ? $row->current : "0";
		$item["total_donations"]		= $row->total_donations;
		$item["currency"]				= $currency;


		if( !property_exists($row, 'campaign_id') || !$row->campaign_id ) {
			$item["enable_donation_levels"]	= $row->enable_donation_levels ? true : false;

			if( is_array($row->donation_levels) ) {
				$levels = array();

				foreach( $row->donation_levels as $lvl ) {

					if ($lvl->status > 0) {
						$levels[] = array(
							'level_id'	=> $lvl->id,
							'amount'	=> $lvl->amount ? $lvl->amount : "0",
							'label'		=> $lvl->description ? $lvl->description : "",
							'position'	=> $lvl->position ? $lvl->position : "0"
						);
					}
				}

				$item['donation_levels'] = $levels;
			} else {
				$item['donation_levels'] = array();
			}

			$item["has_giving_opportunities"]	= $row->has_giving_opportunities ? true : false;
			$item['total_opportunities']		= $row->total_opportunities ? $row->total_opportunities : "0";
		}

		if( $row->campaign_image_file_name ) {
			// is opportunity, so we have to generate the image URL
			$ext = substr(
				$row->campaign_image_file_name,
				strrpos($row->campaign_image_file_name, '.')+1
			);
			if( !$container_uri ) {
				$container_uri = _fetch_cf_uri();
			}

			$item['image_url'] = $container_uri.'/'.$row->id.'/_original.'.$ext;
			$item['thumb_url'] = $container_uri.'/'.$row->id.'/_thumb.'.$ext;
		} else {
			$item['image_url'] = "";
			$item['thumb_url'] = "";
		}

		$item['youtube_id']			= $row->youtube ? $row->youtube : "";


		if( !property_exists($row, 'campaign_id') || !$row->campaign_id ) {
			$item['analytics_id']				= $row->analytics_id;
			$item['campaign_color']				= $row->campaign_color;
			$item['header_font_color']			= $row->header_font;
			$item["display_donation_target"]	= $row->display_donation_target ? true : false;
			$item["display_donation_total"]		= $row->display_donation_total ? true : false;

			$item['receipt'] = array(
				'send_receipt'			=> $row->send_receipt ? true : false,
				'email_org_name'		=> $row->email_org_name ? $row->email_org_name : "",
				'reply_to_address'		=> $row->reply_to_address ? $row->reply_to_address : "",
				'bcc_address'			=> $row->bcc_address ? $row->bcc_address : "",
				'street_address'		=> $row->street_address ? $row->street_address : "",
				'street_address_2'		=> $row->street_address_2 ? $row->street_address_2 : "",
				'city'					=> $row->city ? $row->city : "",
				'state'					=> $row->state ? $row->state : "",
				'postal_code'			=> $row->postal_code ? $row->postal_code : "",
				'country'				=> $row->country ? $row->country : "",
				'receipt_body'			=> $row->receipt_body ? $row->receipt_body : "",
			);

			$item['custom_fields'] = array();

			if( $row->custom_fields ) {
				$fields = array();
				foreach( $row->custom_fields as $field ) {
					$fields[] = array(
						'field_id'		=> $field->id,
						'field_type'	=> $field->field_type ? 'text' : 'dropdown',
						'field_label'	=> $field->field_label ? $field->field_label : "",
						'options'		=> $field->field_type ? array() : explode("\n", $field->field_options),
						'position'		=> $field->position ? $field->position : "0",
						'status'		=> $field->status ? true : false,
						'required'		=> $field->field_required ? true : false
					);
				}

				$item['custom_fields'] = $fields;
			}

			$item['campaign_fields'] = array();

			if( $row->campaign_fields ) {
				$fields = array();
				foreach( $row->campaign_fields as $field ) {
					$fields[] = array(
						'field_id'		=> $field->id,
						'field_type'	=> $field->field_type ? 'text' : 'dropdown',
						'field_label'	=> $field->field_label ? $field->field_label : "",
						'options'		=> $field->field_type ? array() : explode("\n", $field->field_options),
						'position'		=> $field->position ? $field->position : "0",
						'status'		=> $field->status ? true : false,
						'required'		=> $field->field_required ? true : false
					);
				}

				$item['campaign_fields'] = $fields;
			}

		} else {
			$item['campaign_responses'] = array();

			if( ($responses = $row->campaign_responses) ) {
				$cr = array();
				foreach( $responses as $resp ) {
					$cr[] = array(
						'campaign_field_id'		=> $resp->campaign_field_id,
						'field_id'				=> $resp->id,
						'field_type'			=> $resp->field_type ? 'text' : 'dropdown',
						'field_label'			=> $resp->field_label ? $resp->field_label : "",
						'response'				=> $resp->user_response ? $resp->user_response : "",
						'status'				=> $resp->status ? true : false
					);
				}
				$item['campaign_responses'] = $cr;
			}

			if( $row->opportunity_id && $row->opportunity_id != $row->id ) {
				$tmp = $row->id;
				$row->id = $row->opportunity_id;
			}
			$item['supporters'] = prep_supporters($row->__supporters(), true);
			if( $row->opportunity_id && $row->opportunity_id != $row->id ) {
				$row->id = $tmp;
			}

		}

		$obj = $row;
		if (property_exists($row, 'campaign_id') && $row->id != $row->campaign_id) {
			$obj = $row->opportunity;
		}
		switch($obj->frequency_type) {
			case 1:
				$item['frequency_type'] = 'recurring';
				break;
			case 2:
				$item['frequency_type'] = 'checkout';
				break;
			case 0:
			default:
				$item['frequency_type'] = 'onetime';
				break;
		}

		switch($obj->frequency_period) {
			case 1:
				$item['frequency_period'] = 'monthly';
				break;
			case 3:
				$item['frequency_period'] = 'quarterly';
				break;
			case 6:
				$item['frequency_period'] = 'biyearly';
				break;
			case 12:
				$item['frequency_period'] = 'yearly';
				break;
			case 0:
			default:
				$item['frequency_period'] = '';
				break;
		}

		if( get_instance()->input->related() && property_exists($row, 'campaign_id')
			&& $recurse ) {

			$item['campaign'] = array_shift(
				prep_campaigns(array($row->campaign), false)
			);

		}

		$out[] = $item;
	}

	return $out;
}

/**
 * Prepare donation object for output
 * @param  Array  $rows     Donation search results
 * @param  boolean $recurse
 * @return Array
 */
function prep_donations($rows, $recurse = true) {
	$out = array();
	if( !is_array($rows) ) {
		$rows = array($rows);
	}

	$acct = get_instance()->authorization->get_account();
	$tz = $acct->timezone;
	$currency = $acct->currency;

	foreach( $rows as $row ) {

		$newTimeZone = new DateTimeZone($tz);
		$date = new DateTime(date('c', strtotime($row->donation_date)));
		$date->setTimeZone($newTimeZone);

		$item = array(
		    "id_token"			=> $row->donation_token,
			// "donation_date"		=> date('c', strtotime($row->donation_date)),
			'donation_date'		=> $date->format('c')
		);

		if( get_instance()->input->related() && property_exists($row, 'campaign_id')
			&& $recurse ) {

			if( $row->campaign ) {
				$item['campaign'] = array_shift(
					prep_campaigns(array($row->campaign), false)
				);
			} elseif( $row->opportunity ) {
				$item['opportunity'] = array_shift(
					prep_campaigns(array($row->opportunity), false)
				);

				$item['opportunity']['campaign'] = array_shift(
					prep_campaigns(array($row->opportunity->campaign), false)
				);

			}
		} elseif( property_exists($row, 'campaign_id') && $recurse ) {
			if( $row->campaign ) {
				$item['campaign'] = $row->campaign->campaign_token;
			} elseif( $row->opportunity ) {
				$item['opportunity'] = $row->opportunity->campaign_token;
			}
		}

		$item["first_name"]			= $row->first_name ? $row->first_name : "";
		$item["last_name"]			= $row->last_name ? $row->last_name : "";
		$item["billing_address1"]	= $row->billing_address1 ? $row->billing_address1 : "";
		$item["billing_city"]		= $row->billing_city ? $row->billing_city : "";
		$item["billing_state"]		= $row->billing_state ? $row->billing_state : "";
		$item["billing_postal_code"]= $row->billing_postal_code ? $row->billing_postal_code : "";
		$item["billing_country"]	= $row->billing_country ? $row->billing_country : "";
		$item["donation_total"]		= $row->amount ? $row->amount : "0";
		$item["currency"]			= $currency;
		// $item["individual_total"]	= $row->amount ? $row->amount : "0";
		$item["donation_level"]		= $row->description ? $row->description : "";
		$item['donation_level_id']	= $row->donation_level_id ? $row->donation_level_id : "";
		$item["contact"]			= $row->contact ? true : false;
		$item["email_address"]		= $row->email_address ? $row->email_address : "";
		$item["offline"]			= $row->offline ? true : false;
		$item['refunded']			= $row->refunded ? true : false;
		// "referrer"			=> $row->referrer ? $row->referrer : "",

		$item['custom_responses'] = array();

		if( ($responses = $row->custom_responses) ) {
			$cr = array();
			foreach( $responses as $resp ) {
				$cr[] = array(
					'field_id'		=> $resp->field_id,
					'field_type'	=> $resp->parent_field_type ? 'text' : 'dropdown',
					'field_label'	=> $resp->field_label ? $resp->field_label : "",
					'response'		=> $resp->user_response ? $resp->user_response : "",
					'status'		=> $resp->status ? true : false
				);
			}
			$item['custom_responses'] = $cr;
		}

		$item['supporter'] = array();
		if( $row->supporter_id ) {
			$item['supporter'] = array_shift(prep_supporters($row->__supporter()));
		}

		if ($row->plan_id) {
			$p = array();
			$p['id'] 						= $row->plan_id;
			$p['stripe_plan_id'] 			= $row->plan->stripe_plan_id;
			$p['stripe_subscription_id']	= $row->plan->stripe_subscription_id;
			$p['currency'] 					= $row->plan->currency;
			$p['frequency_type'] 			= $row->plan->frequency_type;
			$p['frequency_period'] 			= $row->plan->frequency_period;
			$p['token'] 					= $row->plan->plan_token;
			$p['stripe_url']				= $row->plan->stripe_url;

			$item['plan'] = $p;
		} else {
			$item['plan'] = array();
		}

		$item['canceled'] = $row->canceled ? true : false;

		$out[] = $item;
	}

	return $out;

}

/**
 * Prepare supporter object for output
 * @param  Array  $rows     Supporter search results
 * @param  boolean $recurse
 * @return Array
 */
function prep_supporters($rows, $show_created = false) {
	$out = array();
	if( !is_array($rows) ) {
		$rows = array($rows);
	}
	$acct = get_instance()->authorization->get_account();
	$tz = $acct->timezone;
	$currency = $acct->currency;

	foreach( $rows as $row ) {
		$item = array(
		    "id_token"			=> $row->supporter_token,
		);

		$item["first_name"]			= $row->first_name ? $row->first_name : "";
		$item["last_name"]			= $row->last_name ? $row->last_name : "";
		$item["email_address"]		= $row->email_address ? $row->email_address : "";
		$item["street_address"]		= $row->street_address ? $row->street_address : "";
		$item["city"]				= $row->city ? $row->city : "";
		$item["state"]				= $row->state ? $row->state : "";
		$item["postal_code"]		= $row->postal_code ? $row->postal_code : "";
		$item["country"]			= $row->country ? $row->country : "";
		$item["donations_total"]	= $row->donations_total;
		$item["total_donations"]	= $row->total_donations;
		$item['currency']			= $currency;
		if( $show_created ) {
			$item['date_added_to_opportunity'] = $row->date_added;
		}
		$out[] = $item;
	}

	return $out;

}

function display_string($s) {
	return $s ? $s : '';
}

function display_bool($v) {
	if( $v ) {
		return 'true';
	}

	return 'false';
}

function _fetch_cf_uri() {
	$ci = get_instance();
	// cloud files
	if (!$ci->config->item('s3_bucket')) {
		return reduce_double_slashes(str_replace('/api/', '/', base_url('/uploads/files/campaigns')));
	}

	return 'https://s3.amazonaws.com/'.$ci->config->item('s3_bucket');
}
