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
            You are donating to <strong><?php echo $campaign->title ?></strong>.
          </h1>
        </div>
      </header>
    </div>

    <div class="checkout-start">

      <form method="post" action="<?php echo site_url('donate/'.$hash.'/process') ?>?utm_source=<?php echo $this->input->get('utm_source') ? $this->input->get('utm_source') : 'direct' ?>&utm_medium=<?php echo $this->input->get('utm_medium') ? $this->input->get('utm_medium') : 'link' ?>&utm_campaign=<?php echo $campaign->campaign ? $campaign->campaign->title : $campaign->title ?>" id="form-donation-checkout">
        <input type="hidden" name="spk" value="<?php echo $this->config->item('stripe_publishable_key') ?>" />
        <?php if( $donation_level ) : ?>
            <input type="hidden" name="donation_level" value="<?php echo $donation_level->id ?>" />
        <?php endif ?>
        <?php if( $additional_donation ) : ?>
            <input type="hidden" name="additional_donation" value="<?php echo $additional_donation ?>" />
        <?php endif ?>
        <?php if( $donation_amount ) : ?>
            <input type="hidden" name="donation_amount" value="<?php echo $donation_amount ?>" />
        <?php endif ?>


        <?php if( $charge_error ) : ?>
            <small class="error">Oops! <?php echo $charge_error ?></small>
        <?php endif ?>
        <div class="row">
          <div class="small-12 medium-6 columns">
              <div class="box">
                <div class="amount">
                  <ul class="unstyled">
                    <li>
                      Donation Amount: <strong><?php echo money_format('%n', $donation_total/100) ?>
                      <?php if( $donation_level ) : ?>
                        - <?php echo $donation_level->description ?>
                      <?php endif ?>
                      </strong>
                    </li>
                  </ul>
                </div>
                <h4>Donor Information</h4>
                <label for="input-firstname">First Name:
                  <input type="text" id="input-firstname" name="first_name" value="<?php echo $this->input->post('first_name') ?>" />
                  <small class="error hide" data-field="first_name">First name is required</small>
                </label>

                <label for="input-lastname">Last Name:</label>
                <input type="text" id="input-lastname" name="last_name" value="<?php echo $this->input->post('last_name') ?>" />
                <small class="error hide" data-field="last_name">Last name is required</small>

                <label for="input-street">Billing Address:</label>
                <input type="text" id="input-street" name="street" value="<?php echo $this->input->post('street') ?>" />
                <small class="error hide" data-field="street">Address is required</small>

                <label for="input-city">City:</label>
                <input type="text" id="input-city" name="city" value="<?php echo $this->input->post('city') ?>" />
                <small class="error hide" data-field="city">City is required</small>

                <div class="row">
                    <div class="small-12 medium-4 columns">
                        <label for="input-state">State:</label>
                        <input type="text" id="input-state" name="state" value="<?php echo $this->input->post('state') ?>" />
                        <small class="error hide" data-field="state">State is required</small>
                    </div>
                    <div class="small-12 medium-4 columns">
                        <label for="input-postal">Postal Code:</label>
                        <input type="text" name="postal" id="input-postal" value="<?php echo $this->input->post('postal') ?>" />
                        <small class="error hide" data-field="postal">Postal code is required</small>
                    </div>
                    <div class="small-12 medium-4 columns">
                        <label for="input-country">Country:</label>
                        <input type="text" name="country" id="input-country" value="<?php echo $this->input->post('country') ?>" />
                        <small class="error hide" data-field="country">Country is required</small>
                    </div>
                </div>

                <label for="input-email">Email:</label>
                <input type="text" name="email" id="input-email" value="<?php echo $this->input->post('email') ?>" />
                <small class="error hide" data-field="email">Email is required</small>
              </div>
          </div>
          <div class="small-12 medium-6 columns">
            <div class="box">
              <h4>Credit Card</h4>

              <label>Card Number:</label>
              <input type="text" name="cc_number" placeholder="5555 5555 5555 5555" class="eight" x-autocompletetype="cc-number" />
              <small class="error hide" data-field="cc_number">Please provide a valid credit card number</small>
              <label>CVC:</label>
              <input type="text" name="cc_cvc" placeholder="555" class="three" />
              <small class="error hide" data-field="cc_cvc">CVC required</small>

              <label>Expiration Date (mm / yyyy):</label>
              <input type="text" name="cc_exp" placeholder="<?php echo date('m / Y') ?>" class="three" />
              <small class="error hide" data-field="cc_exp">Expiration required</small>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="box checkout">
            <div class="small-12 columns right">
              <hr>
              <input type="submit" value="Checkout" />
            </div>
          </div>
        </div>

      </form>
    </div> <!-- eo checkout-start -->
  </div>
</div>

<script type="text/javascript" src="https://js.stripe.com/v1/"></script>
<?php $this->load->view('components/_external_footer') ?>
