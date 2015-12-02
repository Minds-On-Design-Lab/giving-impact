<?php $this->load->view('components/_header') ?>
<?php $this->load->view('components/_nav') ?>

<div id="page">
    <div id="page-header">
        <div class="row">
            <div class="small-12 columns">
                <h1 class="page-title">Account Settings</h1>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="small-12 medium-9 columns">
            <div class="box">

                <h2>
                    Organization Settings
                    <a href="<?php echo site_url('account') ?>" class="right"><i class="fa fa-times-circle action"></i></a>
                </h2>

                <form method="post" action="<?php echo site_url('account/edit/org') ?>" enctype="multipart/form-data">

                    <div class="row">
                        <div class="small-12 medium-8 columns">
                            <label for="input-account-name"<?php echo form_error('account_name') ? ' class="error"' : '' ?>>Account Name<span class="required">*</span></label>
                            <input type="text" name="account_name" id="input-account_name" value="<?php echo $this->input->post('account_name') ? $this->input->post('account_name') : $account->account_name ?>"<?php echo form_error('account_name') ? ' class="error"' : '' ?> />
                        </div>
                    </div>

                    <div class="row">
                        <div class="small-12 medium-8 columns">
                            <label>Address</label>
                            <input type="text" name="street_address" placeholder="Street Address" value="<?php echo $this->input->post('street_address') ? $this->input->post('street_address') : $account->street_address ?>" />
                            <input type="text" name="street_address_2" placeholder="Street Address Line 2" value="<?php echo $this->input->post('street_address_2') ? $this->input->post('street_address_2') : $account->street_address_2 ?>" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="small-12 medium-8 columns">
                          <div class="row">
                            <div class="small-12 medium-6 columns">
                                <input type="text" name="city" placeholder="City" value="<?php echo $this->input->post('city') ? $this->input->post('city') : $account->city ?>" />
                            </div>
                            <div class="small-12 medium-2 columns">
                                <input type="text" name="state" placeholder="State" value="<?php echo $this->input->post('state') ? $this->input->post('state') : $account->state ?>" />
                            </div>
                            <div class="small-12 medium-4 columns">
                                <input type="text" name="mailing_postal_code" value="<?php echo $this->input->post('mailing_post_code') ? $this->input->post('mailing_post_code') : $account->mailing_postal_code ?>" placeholder="ZIP" />
                            </div>
                          </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="small-12 medium-8 columns">
                            <label for="input-file">Logo Image</label>
                            <p class="directions">File size should be no more than 185 pixels wide and 85 pixels high. We recommend you save your file as a web optimized jpeg, gif or png.</p>

                            <?php if( $account->image_url ) : ?>
                                <img src="<?php echo $account->thumb_url ?>" id="campaign-image-thumb">
                                <br />
                            <?php endif ?>
                            <input type="file" name="file" id="input-file" />
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
            </div> <!--eo box-->
        </div>
        <div class="small-12 medium-3 columns">
            &nbsp;
        </div>
    </div>
</div> <!-- eo page -->

<?php $this->load->view('components/_footer') ?>
