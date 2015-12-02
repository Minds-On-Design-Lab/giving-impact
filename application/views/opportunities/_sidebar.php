<div>
    <ul class="simple-list metrics">
    	<li>Target: <span class="value"><?php echo money_format('%n', $opportunity->donation_target/100) ?></span></li>
    	<li>Current: <span class="value"><?php echo money_format('%n', $opportunity->donation_total/100) ?></span></li>
    	<li>Donations: <span class="value"><?php echo $opportunity->total_donations ?></span></li>
	</ul>
     <div class="box">
       <h3>Checkout URL:</h3>
        <p>This links directly to the donation checkout page for this Giving Opportunity</p>

        <input type="text" class="input-text" disabled value="<?php echo $opportunity->donation_url ?>" />
    </div> <!-- eo box -->

</div>
