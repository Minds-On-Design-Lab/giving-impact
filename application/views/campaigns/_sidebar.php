<div>
    <ul class="simple-list metrics side-bar">
    	<li>Target: <span class="value"><?php echo money_format('%n', $campaign->donation_target/100) ?></span></li>
    	<li>Current: <span class="value"><?php echo money_format('%n', $campaign->donation_total/100) ?></span></li>
    	<?php if ($campaign->has_giving_opportunities) : ?><li>Opportunties: <span class="value"><?php echo $campaign->total_opportunities ?></span></li><?php endif ?>
    	<li>Donations: <span class="value"><?php echo $campaign->total_donations ?></span></li>
	</ul>
    <div class="box side-bar">
    	<h3>Checkout URL:</h3>
        <p>This links directly to the donation checkout page for this Campaign</p>
    	<input type="text" value="<?php echo $campaign->donation_url ?>" disabled />
	</div>

	<div class="box side-bar">
	    <h3>Campaign Token:</h3>
	    <input type="text" value="<?php echo $campaign->id_token ?>" disabled />
	    <p>The token is used to identify the campaign when using the API</p>
	</div>
</div>
