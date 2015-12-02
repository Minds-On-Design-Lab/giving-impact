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
            <div class="box">
                <h2>
                    Email Receipt Settings
                    <a href="<?php echo site_url('campaigns/'.$campaign->id_token.'/edit') ?>" class="right"><i class="fa fa-times-circle action"></i></a>
                </h2>

                <form method="post" action="<?php echo site_url('campaigns/'.$campaign->id_token.'/edit/receipt') ?>" id="form-edit-receipt">

                    <div class="row">
                        <div class="small-12 medium-8 columns">
                            <label<?php echo form_error('send_receipt') ? ' class="error"' : '' ?>>Email Donation Receipt Status<span class="required">*</span></label>
                            <label for="input-send_receipt" class="label-checkbox">
                                <input type="radio" name="send_receipt" value="1" id="input-send_receipt"<?php if( $this->input->post('send_receipt') || $campaign->receipt->send_receipt ) : ?> checked<?php endif ?>/>
                                Send automated donation receipt emails
                            </label>
                            <label for="input-no-send_receipt" class="label-checkbox">
                                <input type="radio" name="send_receipt" value="0" id="input-no-send_receipt"<?php if( !$campaign->receipt->send_receipt || (!$campaign->id_token && !count($_POST)) ) : ?> checked<?php endif ?> />
                                Do not send automated donation receipt emails
                            </label>
                        </div>
                    </div>

                    <div id="container-reciept-settings">
                        <div class="row">
                            <div class="small-12 medium-8 columns">
                                <label for="input-email_org_name"<?php echo form_error('email_org_name') ? ' class="error"' : '' ?>>Organization Name for Receipt<span class="required">*</span></label>
                                <input type="text" name="email_org_name" id="input-email_org_name" value="<?php echo $this->input->post('email_org_name') ? $this->input->post('email_org_name') : $campaign->receipt->email_org_name ?>"<?php echo form_error('email_org_name') ? ' class="error"' : '' ?> />
                            </div>
                        </div>

                        <div class="row">
                            <div class="small-12 medium-8 columns">
                                <label for="input-reply_to_address"<?php echo form_error('reply_to_address') ? ' class="error"' : '' ?>>Reply-to Email Address<span class="required">*</span></label>
                                <input type="text" name="reply_to_address" id="input-reply_to_address" value="<?php echo $this->input->post('reply_to_address') ? $this->input->post('reply_to_address') : $campaign->receipt->reply_to_address ?>"<?php echo form_error('reply_to_address') ? ' class="error"' : '' ?> />
                            </div>
                        </div>

                        <div class="row">
                            <div class="small-12 medium-8 columns">
                                <label for="input-bcc_address">BCC Email Address</label>
                                <input type="text" name="bcc_address" id="input-bcc_address" value="<?php echo $this->input->post('bcc_address') ? $this->input->post('bcc_address') : $campaign->receipt->bcc_address ?>" />
                            </div>
                        </div>

                         <div class="row">
                            <div class="small-12 medium-8 columns">
                                <label>Address</label>
                                <input type="text" name="street_address" value="<?php echo $this->input->post('street_address') ? $this->input->post('street_address') : $campaign->receipt->street_address ?>" placeholder="Street Address" />
                                <input type="text" name="street_address_2" value="<?php echo $this->input->post('street_address_2') ? $this->input->post('street_address_2') : $campaign->receipt->street_address_2 ?>" placeholder="Street Address 2nd Line" />
                                <div class="row">
                                    <div class="small-12 medium-6 columns">
                                        <input type="text" name="city" value="<?php echo $this->input->post('city') ? $this->input->post('city') : $campaign->receipt->city ?>" placeholder="City" />
                                    </div>
                                    <div class="small-12 medium-4 columns">
                                        <input type="text" name="state" value="<?php echo $this->input->post('state') ? $this->input->post('state') : $campaign->receipt->state ?>" placeholder="State" />
                                    </div>
                                    <div class="small-12 medium-2 columns">
                                        <input type="text" name="zip" value="<?php echo $this->input->post('zip') ? $this->input->post('zip') : $campaign->receipt->postal_code ?>" placeholder="Zip" />
                                    </div>
                                </div>
                                <input type="text" name="country" value="<?php echo $this->input->post('country') ? $this->input->post('country') : $campaign->receipt->country ?>" placeholder="Country"/>
                            </div>
                        </div>

                        <div class="row">
                            <div class="small-12 medium-8 columns">
                                <label for="input-receipt">Receipt Body</label>
                                <textarea rows="10" id="input-receipt" name="receipt_body"><?php echo $this->input->post('receipt_body') ? $this->input->post('receipt_body') : $campaign->receipt->receipt_body ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="small-12 medium-8 columns">
                            <hr />
                            <div class="row">
                                <div class="small-6 columns">
                                    <p class="form-meta"><span class="required">*</span> Denotes required field</p>
                                </div>
                                <div class="small-6 columns form-submit">
                                    <input type="submit" value="Save Settings" class="gi-button" />
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div> <!-- eo box -->
        </div>
        <div class="small-12 medium-3 columns">
        </div>
    </div>
</div>

<?php $this->load->view('components/_footer') ?>
