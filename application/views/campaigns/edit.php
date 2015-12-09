<?php $this->load->view('components/_header') ?>
<?php $this->load->view('components/_nav') ?>

<?php $this->load->view('campaigns/_nav', array(
    'active' => false
)) ?>

<div id="page">
    <div id="page-header">
        <div class="row">
            <div class="small-12 columns">
                <h1 class="page-title"><?php echo $campaign->title ?></h1>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="small-12 medium-9 columns">

            <a name="basic-settings"></a>
            <div class="box">
                <h2>Campaign Information
                     <span><a href="<?php echo site_url('campaigns/'.$campaign->id_token.'/edit/basic') ?>" class="gi-button">Edit Basic Settings</a></span>
                </h2>

                <dl>
                    <dt>Title</dt>
                    <dd><?php echo $campaign->title ?></dd>

                    <dt>Target</dt>
                    <dd><?php echo money_format('%n',$campaign->donation_target/100) ?></dd>

                    <dt>Minimum Donation</dt>
                    <dd><?php echo money_format('%n',$campaign->donation_minimum/100) ?></dd>

                    <dt>Description</dt>
                    <dd><p><?php echo $campaign->description ?></p></dd>

                    <dt>Donation Options</dt>
                    <dd><?php echo $campaign->enable_donation_levels ? 'Donation levels' : 'Open Input' ?></dd>

                    <dt>Campaign Type</dt>
                    <dd><?php echo $campaign->has_giving_opportunities ? 'Multiple Giving Opportunities' : 'Single Giving Opportunity' ?></dd>

                    <dt>Campaign Status</dt>
                    <dd><?php echo $campaign->status ? 'Open' : 'Closed' ?></dd>

                    <dt>Donation Frequency</dt>
                    <dd>
                        <?php if( !$campaign->frequency_type || $campaign->frequency_type == 'onetime' ) : ?>
                            Only one time
                        <?php elseif( $campaign->frequency_type == 'recurring' ) : ?>
                            Only recurring
                        <?php else : ?>
                            Decide at checkout
                        <?php endif ?>
                    </dd>
                    <?php if( $campaign->frequency_type && $campaign->frequency_type != 'onetime' ) : ?>
                        <dt>Frequency Interval</dt>
                        <dd>
                            <?php if( $campaign->frequency_period == 'monthly' ) : ?>
                                Monthly
                            <?php elseif( $campaign->frequency_period == 'quarterly' ) : ?>
                                Every 3 months
                            <?php elseif( $campaign->frequency_period == 'biyearly' ) : ?>
                                Every 6 months
                            <?php else : ?>
                                Yearly
                            <?php endif ?>
                        </dd>
                    <?php endif ?>
                </dl>

            </div>

            <a name="design-communications"></a>
            <div class="box">
                <h2>
                    Design &amp; Communication Settings
                    <span><a href="<?php echo site_url('campaigns/'.$campaign->id_token.'/edit/design') ?>" class="gi-button">Edit Design &amp; Communication Settings</a></span>
                </h2>

                <dl>
                    <dt>Campaign Image</dt>
                    <dd><img src="<?php echo $campaign->thumb_url ?>"></dd>

                    <dt>YouTube Video</dt>
                    <dd><?php if($campaign->youtube_id) : ?>http://www.youtube.com/watch?v=<?php echo $campaign->youtube_id ?><?php endif ?></dd>

                    <dt>Google Analytics Profile ID</dt>
                    <dd><?php echo $campaign->analytics_id ?></dd>

                    <dt>Display Campaign Target</dt>
                    <dd><?php echo $campaign->display_donation_target ? 'Yes' : 'No' ?></dd>

                    <dt>Display Current Total</dt>
                    <dd><?php echo $campaign->display_donation_total ? 'Yes' : 'No' ?></dd>
                </dl>
            </div>

            <a name="email-receipt"></a>
            <div class="box">
                <h2>
                    Email Receipt
                    <span><a href="<?php echo site_url('campaigns/'.$campaign->id_token.'/edit/receipt') ?>" class="gi-button">Edit Receipt Settings</a></span>
                </h2>

                <dl>
                    <dt>Email Donation Receipt Status</dt>
                    <dd><?php echo $campaign->receipt->send_receipt ? 'Send donation receipt emails' : 'Do not send donation receipt emails' ?></dd>
                    <?php if ($campaign->receipt->send_receipt) { ?>
                    <dt>Organization Name for Receipt</dt>
                    <dd><?php echo $campaign->receipt->email_org_name ?></dd>

                    <dt>Reply-to Email Address</dt>
                    <dd><?php echo $campaign->receipt->reply_to_address ?></dd>

                    <dt>BCC Email Address</dt>
                    <dd><?php echo $campaign->receipt->bcc_address ?></dd>

                    <dt>Address</dt>
                    <dd><p>
                        <?php if( $campaign->receipt->street_address ) : ?>
                            <?php echo $campaign->receipt->street_address ?><br />
                            <?php echo $campaign->receipt->street_address_2 ?><br />
                            <?php echo $campaign->receipt->city ?>, <?php echo $campaign->receipt->state ?><br />
                            <?php echo $campaign->receipt->postal_code ?>
                        <?php endif ?>
                    </p></dd>

                    <dt>Receipt Body</dt>
                    <dd><?php echo $this->typography->auto_typography($campaign->receipt->receipt_body) ?></dd>
                    <?php } ?>
                </dl>
            </div>

            <a name="custom-fields"></a>
            <div class="box">
                <h2>
                    Custom Donation Fields
                    <span><a href="<?php echo site_url('campaigns/'.$campaign->id_token.'/edit/fields') ?>" class="gi-button">Edit Donation Fields</a></span>
                </h2>
                <?php if ($campaign->custom_fields) { ?>
                <table class="twelve">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Label</th>
                            <th>Options</th>
                        </tr>
                    </thead>
                    <tbody id="custom-fields">
                    <?php if( $campaign->custom_fields ) : foreach( $campaign->custom_fields as $field ) : ?>
                        <tr>
                            <td data-field="type" class="width10">
                                <span><?php echo $field->field_type ?></span>
                            </td>
                            <td data-field="label" class="width50"><?php echo $field->field_label ?></td>
                            <td data-field="options" class="width40"><?php echo $field->options ? implode("<br />", $field->options) : '' ?></td>
                        </tr>
                    <?php endforeach; endif ?>
                    </tbody>
                </table>
            <?php } else { echo "<dl><p>There are currently no custom donation fields configured</p></dl>"; } ?>
            </div>

            <?php if ($campaign->has_giving_opportunities) : ?>
            <a name="custom-campaign-fields"></a>
            <div class="box">
                <h2>
                    Custom Campaign Fields
                    <span><a href="<?php echo site_url('campaigns/'.$campaign->id_token.'/edit/campaign_fields') ?>" class="gi-button">Edit Campaign Fields</a></span>
                </h2>
                <?php if ($campaign->campaign_fields) { ?>
                <table role="grid">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Label</th>
                            <th>Options</th>
                        </tr>
                    </thead>
                    <tbody id="custom-fields">
                    <?php if( $campaign->campaign_fields ) : foreach( $campaign->campaign_fields as $field ) : ?>
                        <tr>
                            <td data-field="type" class="width10">
                                <span><?php echo $field->field_type ?></span>
                            </td>
                            <td data-field="label" class="width50"><?php echo $field->field_label ?></td>
                            <td data-field="options" class="width40"><?php echo $field->options ? implode("<br />", $field->options) : '' ?></td>
                        </tr>
                    <?php endforeach; endif ?>
                    </tbody>
                </table>
            <?php } else { echo "<dl><p>There are currently no custom campaign fields configured</p></dl>"; } ?>
            </div>
        <?php endif ?>
        </div>
        <div class="small-12 medium-3 columns" id="right">
            <h3>Manage Campaign Settings</h3>
            <ul class="simple-list">
                <li><a href="#basic-settings">Basic Settings</a></li>
                <li><a href="#design-communications">Design &amp; Communication Settings</a></li>
                <li><a href="#email-receipt">Email Receipt</a></li>
                <li><a href="#custom-fields">Custom Donation Fields</a></li>
                <?php if ($campaign->has_giving_opportunities) : ?><li><a href="#custom-campaign-fields">Custom Campaign Fields</a></li><?php endif ?>
            </ul>

        </div>
    </div>
</div> <!-- eo page -->


<?php $this->load->view('components/_footer') ?>
