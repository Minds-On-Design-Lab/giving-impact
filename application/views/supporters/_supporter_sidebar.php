<div>
  <div class="box side-bar">
  	<h3>Supporter Information</h3>
    <?php if ($donation->supporter) : ?>
        <p>
            <a href="<?php echo site_url('supporters/'.$donation->supporter->id_token) ?>"><?php echo $donation->supporter->first_name ?> <?php echo $donation->supporter->last_name ?></a><br />
            <?php echo $donation->supporter->email_address ?>
        </p>
        <p>
            <?php echo $donation->supporter->street_address ?><br />
            <?php echo $donation->supporter->city ?>, <?php echo $donation->supporter->state ?> <?php echo $donation->supporter->postal_code ?><br />
            <?php echo $donation->supporter->country ?>
        </p>
		<h3>Supporter Activity</h3>
		<ul class="side-bar">
		    <li>Total: <span class="value"><?php echo money_format('%n', $donation->supporter->donations_total/100) ?></span></li>
		    <li>Donations: <span class="value"><?php echo $donation->supporter->total_donations ?></span></li>
		</ul>
    <?php else : ?>
        <p><em>Supporter information not found</em></p>
    <?php endif ?>
  </div>
</div>
