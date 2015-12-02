<?php

/**
 * Sends receipt to donor, according to campaign settings.
 *
 * Loads 'notifications/_donation_receipt' view with
 *  * donation Donation_model
 *  * campaign Campaign_model
 *  * account Account_model
 * @param  Object $donation
 * @param  Object $campaign
 * @param  Object $account
 */
function notify_receipt($donation, $campaign, $account) {

	$CI =& get_instance();
	$CI->load->library('email');
	$handler = false;
	$CI->load->library('email');
	$handler = $CI->email;

	$subject = '['.$campaign->title.'] Thank you for your recent donation';
	$from = $CI->config->item('email_from');
	$from = sprintf('Giving Impact <%s>', $from);

	$email_opts = $CI->config->item('email_opts');

	$account->thumb_url = _find_account_thumb($account);

	$message = $CI->load->view(
		'notifications/_donation_receipt',
		array('donation' => $donation, 'campaign' => $campaign, 'account' => $account),
		true
	);

	$org_name = '';
	if( isset($campaign->campaign_id) && isset($campaign->campaign->email_org_name) ) {
		$org_name = $campaign->campaign->email_org_name;
	} elseif( isset($campaign->email_org_name) ) {
		$org_name = $campaign->email_org_name;
	}

	$handler->initialize($email_opts);
	$handler->from($from, $org_name);

	if( isset($campaign->campaign_id) && isset($campaign->campaign->reply_to_address) ) {
		$handler->reply_to($campaign->campaign->reply_to_address);
	} elseif( isset($campaign->reply_to_address) ) {
		$handler->reply_to($campaign->reply_to_address);
	}

	if( isset($campaign->campaign_id) && isset($campaign->campaign->bcc_address) ) {
		$handler->bcc($campaign->campaign->bcc_address);
	} elseif( isset($campaign->bcc_address) ) {
		$handler->bcc($campaign->bcc_address);
	}

	$handler->to($donation->email_address);
	$handler->subject($subject);
	$handler->message($message);

	$handler->send();
}

function _find_account_thumb($account) {
	$CI =& get_instance();
	$CI->load->helper('display_helper');
	if( $account->logo_file_name ) {
			// is opportunity, so we have to generate the image URL
			$ext = substr(
				$account->logo_file_name,
				strrpos($account->logo_file_name, '.')+1
			);

			$container_uri = _fetch_cf_uri();

			$thumb_url = $container_uri.'/logos-'.$account->id.'-_thumb.'.$ext;
		} else {
			$thumb_url = "";
		}

    return $thumb_url;
}