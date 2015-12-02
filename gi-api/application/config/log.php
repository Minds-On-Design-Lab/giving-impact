<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config = array(

	'date_format'		=> 'j/M/Y:H:i:s O',

	// minium level which to send an email
	'min_warn'			=> Log::ERROR,

	// minimum level to generate a back trace (all DEBUG messages will generate a trace)
	'min_trace'			=> Log::ERROR,

	// who get's the email
	'warn_email'		=> 'mikej@mod-lab.com',

	// who is the email from
	'warn_email_from'	=> 'mikej@mod-lab.com',

	// warning email subject prefix
	'warn_subject'		=> '[GIAPI] Log Notification',
);