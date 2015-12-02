<?php $this->load->view('notifications/_notify_header') ?>
			
		<br>
		<?php echo $account->first_name ?> <?php echo $account->last_name ?>,
		<br><br>
		
	    We've recently received a request to reset the password associated with the acount for <strong><?php echo $account->account_name ?></strong>. If you'd like to continue and reset your password, please visit the following URL.
	    <br><br>
	    <a href="<?php echo site_url('forgot/'.$user->change_request) ?>"><?php echo site_url('forgot/'.$user->change_request) ?></a>
		<br><br>
		
		If you didn't make this request, you can safely ignore this message. Your password has not been altered in any way.
		<br><br>

		Thank you,<br><br>
		Giving Impact Support<br>
		http://givingimpact.com
		<br><br>

<?php $this->load->view('notifications/_notify_footer') ?>