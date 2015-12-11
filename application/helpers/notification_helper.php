<?php
/**
 * Notification helper
 */

/**
 * Notify user of lost password reset request.
 *
 * Loads 'notifications/_lost_password' view with
 *  * user User_model
 *  * account Account_model
 *
 * @param  Object $user    User_model
 * @param  Object $account Account_model
 */
function notify_lost_password($user, $account) {

	$CI =& get_instance();
	$CI->load->library('email');

	if (!$CI->config->item('email_from')) {
		return;
	}

	$handler = false;
	$CI->load->library('email');
	$handler = $CI->email;

	$subject = sprintf('[Giving Impact] Password Change Request');
	$from = $CI->config->item('email_from');
	$from = sprintf('Giving Impact <%s>', $from);

	$email_opts = $CI->config->item('email_opts');

	$message = $CI->load->view(
		'notifications/_lost_password',
		array('user' => $user, 'account' => $account),
		true
	);

	$handler->initialize($email_opts);
	$handler->from($from);

	$handler->to($user->email);
	$handler->subject($subject);
	$handler->message($message);

	$handler->send();

}

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

	if (!$CI->config->item('email_from')) {
		return;
	}

	$handler = false;
	$CI->load->library('email');
	$handler = $CI->email;

	$subject = '['.$campaign->title.'] Thank you for your recent donation';
	$from = $CI->config->item('email_from');
	$from = sprintf('Giving Impact <%s>', $from);

	$email_opts = $CI->config->item('email_opts');

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

/**
 * Sends cancel notification to donor
 *
 * Loads 'notifications/_donation_subscription_canceled' view with
 *  * donation Donation_model
 *  * campaign Campaign_model
 *  * account Account_model
 * @param  Object $donation
 * @param  Object $campaign
 * @param  Object $account
 */
function notify_subscription_canceled($donation, $campaign, $account) {

	$CI =& get_instance();
	$CI->load->library('email');

	if (!$CI->config->item('email_from')) {
		return;
	}

	$handler = false;
	$CI->load->library('email');
	$handler = $CI->email;

	$subject = '['.$campaign->title.'] Your recurring donation has been canceled';
	$from = $CI->config->item('email_from');
	$from = sprintf('Giving Impact <%s>', $from);

	$email_opts = $CI->config->item('email_opts');

	$message = $CI->load->view(
		'notifications/_donation_subscription_canceled',
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
	// echo $handler->print_debugger();

}
