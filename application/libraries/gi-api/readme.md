givingimpact-php
=========================

## Overview

A PHP library to interact with Giving Impact&trade;. Giving Impact is an online fundraising platform driven by a thoughtful API to allow designers and developers to deliver customized online donation experiences for Non-profits easily, affordable, and flexibly.

For more about Giving Impact and to view our full documentation and learning reasources please visit [givingimpact.com](http://givingimpact.com)


## Library Credits

**Developed By:** Minds On Design Lab - http://mod-lab.com<br />
**Version:** 1.1<br />
**Copyright:** 2012 - 2013 Minds On Design Lab<br />
**License:** Licensed under the MIT license - Please refer to LICENSE<br />

## Requirements

* PHP 5.3+
* PHP cURL with SSL support

**Note:** This library will not work in PHP 5.2 and below.

## Configuration

You will need a valid Giving Impact Account API key, accessible from the Account Settings area.

First, add the library.

    require_once "givingimpact-php/MODL/GivingImpact.php";

Second, create your namespace and set both a name for your site/application followed by your Giving Impact API key.

    $gi = new \MODL\GivingImpact('MY-APPLICATION-NAME', 'MY_PRIVATE_KEY');

## Available Methods

The following is a list of available methods; however, for full details about each method, please refer to the [API Documentation](http://givingimpact.com/docs).

 * campaigns
   * create new
   * fetch single
   * fetch multiple
   * fetch related opportunities
   * fetch related donations
   * fetch related statistic logs
 * opportunities
   * create new
   * fetch single
   * fetch multiple
   * fetch related donations
   * fetch related statistic logs
 * donations
   * create offline
 * supporters
   * fetch single
   * fetch multiple
   * fetch related donations
 * custom checkout

## Docs

Keep in mind, the Giving Impact API Library uses a fluent interface, so you can easily chain your methods together.

### Common

For a full list of all Campaign, Opportunity, Donation or Stat properties, please refer to the [API Documentation](http://givingimpact.com/docs).

Campaigns, Opportunities, Donations and Shares all share the following methods

**limit(int limit)**

Limit the number of results returned

    $campaigns = $gi->campaign
        ->limit(5);

**offset(int offset)**

Skip a number of records, useful in combination with `limit` for pagination

    $campaigns = $gi->campaign
        ->limit(10)
        ->offset(5);

**sort(string sort_by)**

Sort the returned results. By default will sort by the provided property ascending. You can change the sort direction by appending `|desc` to the end of the property.

    $campaigns = $gi->campaign
        ->sort('created_at');  // Sorts by created time ASCENDING

    $campaigns = $gi->campaign
        ->sort('created_at|desc'); // Sorts by created time DESCENDING

**status(boolean stats)**

Get only campaigns/opportunites of a particular status. By default the API returns only items with a status of `active`. Can be `active`, `inactive` or `both`.

    $campaigns = $gi->campaign
        ->status('active');

**fetch([string token])**

This is the last method you call in any query. This tells the library to construct the full URI and query the API. Once you call this method all the other filters will be reset.

    $campaigns = $gi->campaign
        ->status('both')
        ->limit(10)
        ->fetch();

    foreach( $campaigns as $campaign ) {
        echo $campaign->title;
    }

Passing a Campaign, Opportunity or Donation token (depending on the model) will return a specific campaign, opportunity or donation.

    $campaign = $gi->campaign
        ->fetch('123456');

    $donation = $gi->donation
        ->fetch('987654');

### Campaigns

Specific methods for Campaigns

**save()**

Create a new campaign. Simply set the properties of a new campaign object to any combination of the following:

 * title **REQUIRED**
 * description **REQUIRED**
 * youtube_id
 * donation_target **REQUIRED**
 * hash_tag
 * status
 * has_giving_opportunities
 * display_donation_target
 * display_donation_total
 * enable_donation_levels
 * donation_levels
 * custom_fields
 * header_font
 * campaign_color
 * donation_minimum
 * analytics_id
 * image_type
 * image_file
 * receipt
   * send_receipt
   * email_org_name
   * reply_to_address
   * bcc_address
   * street_address
   * street_address_2
   * city
   * state
   * postal_code
   * country
   * receipt_body

For example

    $newCampaign = $gi->campaign;
    $newCampaign->title             = 'My campaign';
    $newCampaign->description       = 'Check out my campaign';
    $newCampaign->donation_target   = '1000';
    $newCampaign->image_type        = 'image/jpg';
    $newCampaign->image_file        = '[BASE64 ENCODED STRING]';
    $newCampaign->receipt           = array
        'send_receipt'      => TRUE,
        'email_org_name'    => 'My Org'
    )
    $newCampaign->save();

**opportunities**

Fetch Opportunities for this Campaign. Note, this is called is a property, not a method

    $campaign = $gi->campaign
        ->fetch('ABC123');

    $opportunities = $campaign
        ->opportunities
        ->limit(10)
        ->fetch();

**donations**

Fetch Donations for this Campaign. Note, this is called is a property, not a method

    $campaign = $gi->campaign
        ->fetch('ABC123');

    $donations = $campaign
        ->donations
        ->limit(10)
        ->fetch();

**stats**

Fetch Stats for this Campaign. Note, this is called is a property, not a method

    $campaign = $gi->campaign
        ->fetch('ABC123');

    $stats = $campaign
        ->stats
        ->limit(4)
        ->fetch();

### Opportunities

Specific methods for Opportuniites

**save()**

Create a new opportunity. Simply set the properties of a new opportunity object to any combination of the following:

  * campaign_token **REQURED**
  * title **REQUIRED**
  * description **REQUIRED**
  * youtube_id
  * donation_target **REQUIRED**
  * hash_tag
  * status
  * image_url
  * campaign
  * image_type
  * image_file

For example

    $newOpportunity = $gi->opportunity;
    $newOpportunity->title             = 'My new giving opp';
    $newOpportunity->description       = 'Check out my opportunity';
    $newOpportunity->donation_target   = '1000';
    $newOpportunity->save();

**campaign**

Fetch this Opportunity's parent Campaign. Note, this is called is a property, not a method

    $campaign = $gi->opportunity
        ->fetch('ABC123')
        ->campaign;

**donations**

Fetch Donations for this Opportunity. Note, this is called is a property, not a method

    $opportunity = $gi->opportunity
        ->fetch('ABC123');

    $donations = $opportunity
        ->donations
        ->limit(10)
        ->fetch();

**stats**

Fetch Stats for this Opportunity. Note, this is called is a property, not a method

    $opportunity = $gi->opportunity
        ->fetch('ABC123');

    $stats = $opportunity
        ->stats
        ->limit(4)
        ->fetch();

### Donations

Specific methods for Donations

**save()**

Create a new **offline** donation.

  * first_name
  * last_name
  * billing_address1
  * billing_city
  * billing_state
  * billing_postal_code
  * billing_country
  * donation_total **Must be 5 or the campaign/opportunity minimum donation amount**
  * contact
  * email_address
  * offline
  * campaign **REQUIRED**
  * opportunity **OR REQUIRED**
  * donation_date

For example

    $newDonation = $gi->donation;
    $newDonation->first_name      = 'Test';
    $newDonation->last_name       = 'Testerson';
    $newDonation->offline         = TRUE;
    $newDonation->donation_total  = 25;
    $newDonation->donation_date   = time();
    $newDonation->save();

**campaign**

Fetch this Donations's parent Campaign. Note, this is called is a property, not a method

    $campaign = $gi->donation
        ->fetch('ABC123')
        ->campaign;

**opportunity**

Fetch this Donations's parent Opportunity, if it exists. Note, this is called is a property, not a method

    $campaign = $gi->donation
        ->fetch('ABC123')
        ->opportunity;

### Supporters

Specific methods for Supporters

**donations**

Fetch Donations for this Supporter. Note, this is called is a property, not a method

    $supporter = $gi->supporter
        ->fetch('ABC123');

    $donations = $supporter
        ->donations
        ->limit(10)
        ->fetch();

### Stats

Fetch Statistics for a Campaign or Opportunity

**campaign**

Fetch this Stat log's parent Campaign. Note, this is called is a property, not a method

**opportunity**

Fetch this Stat log's parent Opportunity, if it exists. Note, this is called is a property, not a method

## Example

    require_once "givingimpact-php/MODL/GivingImpact.php";

    $gi = new \MODL\GivingImpact('MY-APPLICATION-NAME', 'MY_PRIVATE_KEY');

    $campaigns = $gi->campaign
        ->limit(10)
        ->fetch();

    $opportunities = $campaigns[3]->opportunities
        ->fetch();

    foreach( $opportunities as $opportunity ) {
        $donations = $opportunity->donations
            ->limit(5)
            ->fetch();

        foreach( $donations as $d ) {
            echo $d->first_name.' '.$d->last_name.'<br />';
        }
    }

You can then copy the resulting files to your CodeIgniter application's `application/libraries` directory.

## Custom Checkout for Donations

This approach gives you incredible flexibility to integrate checkout into your online fundraising experience. There are a collection of requirements to enable your checkout form as detailed below; however, how you build and integrate your form is up to you. In collaboration with our payment processor, Stripe.com, there is a particular setup for credit card processing which ensure consistency of security with our hosted option and in line with Stripe’s requirements that in turn greatly lesson the security compliance burden for you.

In short, this setup ensures that credit card data does not touch your server (let alone Giving Impact’s) on its way to Stripe.com.


* cc_number **required**, valid credit card number
* cc_exp **required**, *MM/YYYY* expiration date
* cc_cvc **required**, valid CVC number from back of card
* campaign -or- opportunity **required**, *string*, unique identifier for the parent campaign or opportunity
* donation_date timestamp, *YYYY-MM-DD HH:MM:SS*, time of donation
* first_name **required**, *string*, donor first name
* last_name **required**, *string*, donor last name
* billing_address1 **required**, *string*, billing address
* billing_city **required**, *string*, billing city
* billing_state **required**, *string*, state
* billing_postal_code **required**, *string*, billing postal code
* billing_country **required**, *string*, billing country
* donation_total **required**, *signed int*, donation amount. **donation amount must be 5 or the campaign donation miniumum amount, whichever is greater
* donation_level_id **integer**, this represents the id of a donation level
* contact **required**, boolean, true/false, default false, used to define if donor opted out of being contacted by email
* email_address **required**, string, email address of donor

### Credit Card Processing Requirements
1. You MUST host your custom checkout page under SSL
2. You need to include our Checkout Javascript and pass your Public API Key (available in Account Settings in the Dashboard).
3. Form input name for your Credit Card Number, Expiration Date, and CVC number must be set to what is showcased in the form example below.
4. Expiration data must be in the form of MM/YYYY


### How it works

**Preliminary Credit Card Processing**

1. Collect the donation and billing information.
2. Pass the credit card information to Giving Impact’s checkout javascript method that coordinates with Stripe to make sure required credit card data is provided and appears valid. If it passes basic validation then Stripe provides a payment token in return.

**Donation and Full Credit Card Processing**

1. Post the donation, billing, and Stripe’s payment token to the /donations API method.
2. If the credit card or any Giving Impact data fails validation for any reason, the API will return an appropriate error.
3. If successful, then the donation is saved and full donation data is returned.

###Preliminary Credit Card Processing Requirements/Approach
* You MUST host your custom checkout page under SSL
* You need to include our Checkout Javascript and pass your Giving Impact Public API Key (available in Account Settings in the Dashboard).
* Form input name for your Credit Card Number, Expiration Date, and CVC number must be set to what is showcased in the example below.
* Expiration data must be in the form of MM/YYYY
*)We’re big fans of Stripe’s [jQuery.payments library](https://stripe.com/blog/jquery-payment) to help improve your credit card forms.

This approach checks that credit card data is well formed, communicates when it is not, and creates a Stripe payment token when it is. This token is what is posted along with other required data to our API and helps to ensure that credit card data never hits your server, let alone ours. The token has all the necessary data encrypted within it for Stripe to read and process.

The code example below details the bullets above.


## Donation Form Example

    <?php

    require_once "givingimpact-php/MODL/GivingImpact.php";

    use MODL\GivingImpact as GIAPI;

    $api = new GIAPI('MY-APPLICATION-NAME','MY_PRIVATE_KEY');
    $campaign $api->campaign->fetch ('CAMPAIGN_OR_OPPORTUNITY_TOKEN');

    // check for a POST object
    if ($_POST) {
      $_POST = $p;
      $donation = $campaign->donation;
      $donation->donation_total = $p[$amount];
      $donation->first_name = $p[first_name];
      $donation->last_name            = $p['last_name'];
      $donation->contact              = $p['fields'];
      $donation->billing_address1     = $p['fields'];
      $donation->billing_city         = $p['fields'];
      $donation->billing_state        = $p['fields'];
      $donation->billing_postal_code  = $p['fields'];
      $donation->billing_country      = 'USA';
      $donation->email_address        = $p['fields'];
      $donation->card                 = $p['token'];

      $donation->custom_responses     = $p['fields'];


      $response = $donation->create();

      if( !$response->isError ) {
          //go to your success template
          header('Location: ./complete.php');
      } else {
          //failure template
          header('Location: ./error.php');
      }

      exit;

    }
    ?>

    <html>
      <head>
        <title> My Title</title>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
      </head>
      <body>

        <form method="post" action="./checkout.php" id="donate-form">
            <label>Donation Amount</label>
            <input type="text" name="amount" />

            <label>First Name</label>
            <input type="text" name="first_name" />

            <label>Last Name</label>
            <input type="text" name="last_name" />

            <label>Address</label>
            <input type="text" name="billing_address1" />

            <label>City</label>
            <input type="text" name="billing_city" />

            <label>State</label>
            <input type="text" name="billing_state" />

            <label>Zip Code</label>
            <input type="text" name="billing_postal_code" />

            <label class="required">Email:</label>
            <input type="text" name="email" value="EMAIL" />

            <label id="may_contact"><input type="checkbox" value="1" name="contact" id="may_contact" /> You may contact me with future updates</label>

            <?php if( $campaign->custom_fields ) : ?>
                <?php foreach( $campaign->custom_fields as $field ) : ?>
                    <?php if( !$field->status ) : continue; endif ; ?>
                    <label><?php echo $field->field_label ?></label>
                    <?php if( $field->field_type == 'text' ) : ?>
                        <input type="text" name="fields[<?php echo $field->field_id ?>]" />
                    <?php else : ?>
                        <select name="fields[<?php echo $field->field_id ?>]">
                            <?php foreach( $field->options as $opt ) : ?>
                                <option value="<?php echo $opt ?>"><?php echo $opt ?></option>
                            <?php endforeach ?>
                        </select>
                    <?php endif ?>
                <?php endforeach ?>
            <?php endif ?>

            <label>Card Number:</label>
            <input type="text" name="cc_number" />
            <label>CVC:</label>
            <input type="text" name="cc_cvc" />

            <label>Expiration Date:</label>
            <input type="text" name="cc_exp" />

            <button class="button" id="process-donation">Checkout</button>
        </form>


        <script type="text/javascript" src="http://api.givingimpact.com/v2/checkout?key=MY_PUBLIC_KEY"></script>
        <script>
            $(function() {

                $('#process-donation').click(function(e) {
                    e.preventDefault();
                    $(this).text('Processing...');
                    $(this).attr('disabled', true);

                    GIAPI.checkout({
                        'card':     $('[name="cc_number"]').val(),
                        'cvc':      $('[name="cc_cvc"]').val(),
                        'month':    $('[name="cc_exp"]').val().substr(0,2),
                        'year':     $('[name="cc_exp"]').val().substr(5,4),
                    }, function(token) {
                        // the card token is returned, append to form and submit
                        $('#donate-form').append($('<input type="hidden" value="'+token+'" name="token" />'));
                        $('#donate-form').submit();
                    });
                })
            });
        </script>




      </body>
    </html>

### Donation and Full Credit Card Processing
You can create a new donation by sending a POST request to the following URI.

<code>/donations</code>

In addition to the authentication and user-agent headers, the following header is also required for POST requests:

<code>Content-Type: application/json</code>

**Example Post Body**

    {
      "campaign": "1234abcde",
      "donation_date": "2013-05-16 20:00:00",
      "first_name": "Greedo",
      "last_name": "TheElder",
      "billing_address1": "100 Best Spot",
      "billing_city": "Mos Eisley Cantina",
      "billing_state": "Tatooine",
      "billing_postal_code": "10001",
      "billing_country": "United States",
      "donation_total": "50.00",
      "donation_level_id": "101",
      "contact": true,
      "email_address": "greedo@givingimpact.com",
      "card": "1234somelongtokenfromstripetostripe"
    }


### Implementation Notes/Tips
Please see [Giving Impact's Docs](http://givingimpact.com/docs/api/donation-checkout) for implementation notes & tips



## Changelog

* 1.1 2013-10-14 - Custom checkout method
* 1.0 2013-08-15 - Added support for custom campaign fields
* 1.0 - Initial Release