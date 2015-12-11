<?php $this->load->view('components/_external_header', array( 'title_action' => 'Donate' )) ?>
<div class="row main">

  <div class="small-12 columns">

    <div class="row">
      <header>
        <div class="small-12 medium-12 large-6 columns end">
          <div class="logo">
            <?php if ($account->image_url) : ?><img src="<?php echo $account->image_url ?>" class="org-logo" /><?php endif ?>
          </div>
          <h1>
            Manage donation for <strong><?php echo $campaign->title ?></strong>.
          </h1>
        </div>
      </header>
    </div>

    <div class="checkout-start">

      <div class="row">
        <div class="small-12 medium-6 columns">
            <div class="box">
              <div class="amount">
                <ul class="unstyled">
                  <li>
                    Donation Amount: <strong><?php echo money_format('%n', $donation->amount/100) ?>
                    <br />
                  </li>
                </ul>
              </div>
              <p>
                Your next donation has been canceled. You will not be charged again.
              </p>
            </div>
        </div>
        <div class="small-12 medium-6 columns">

          <div class="box">
              <h4>Donor Information</h4>
              <label>
                Name: <?php echo $donation->first_name ?> <?php echo substr($donation->last_name, 0, 1) ?>.
              </label>
              <label>
                <?php
                  $parts = explode('@', $donation->email_address);
                  if (strlen($parts[0]) <= 5) {
                    $email = $parts[0]{0}.str_repeat('*', strlen($parts[0]-1)).'@'.$parts[1];
                  } elseif (strlen($parts[0]) <= 10) {
                    $len = strlen($parts[0]);
                    $trim = substr($parts[0], 2, rand(2, $len-2));
                    $email = str_replace($trim, str_repeat('*', strlen($trim)), $parts[0]).'@'.$parts[1];
                  } else {
                    $len = strlen($parts[0]);
                    $trim = substr($parts[0], 4, -6);
                    $email = str_replace($trim, str_repeat('*', strlen($trim)), $parts[0]).'@'.$parts[1];
                  }

                ?>
                Email: <?php echo $email ?>
              </label>
          </div>
        </div>
      </div>

    </div> <!-- eo checkout-start -->
  </div>
</div>

<script type="text/javascript" src="https://js.stripe.com/v1/"></script>
<?php $this->load->view('components/_external_footer') ?>
