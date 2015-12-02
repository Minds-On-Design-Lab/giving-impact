<?php $this->load->view('components/_external_header', array( 'title_action' => 'Donate', 'show_og' => true )) ?>
<div class="row main">

  <div class="small-12 columns">
      <div class="row">
        <header>
          <div class="small-12 medium-6 columns end">
            <div class="logo">
              <?php if ($account->image_url) : ?><img src="<?php echo $account->image_url ?>" class="org-logo" /><?php endif ?>
            </div>
            <h1>
              Thank you from <strong><?php echo $campaign->title ?></strong>.
            </h1>
          </div>
        </header>
      </div>


      <div class="checkout-start">
        <div class="row">
          <div class="small-12 medium-6 columns">
            <div class="box">
              <h4>Email Follow Up</h4>

              <p>
                  <strong><?php echo $account->account_name ?></strong> may want to follow up with you via email to say thank you or share updates. If you would prefer not to receive such communications, please uncheck the box below.
              </p>

              <h5><?php echo $donation->email_address ?></h5>

              <form id="form-contact" action="<?php echo site_url('donate/'.$donation->donation_token.'/contact') ?>">
                  <label for="input_contact">
                      <input type="checkbox" value="1" name="contact"<?php if( $donation->contact ) : ?> checked<?php endif ?> id="input_contact" />
                      Yes you may keep me updated
                  </label>
              </form>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('components/_external_footer', array(
    'track_trans' => array(
        'campaign'  => $campaign,
        'account'   => $account,
        'donation'  => $donation
        )
    )) ?>
