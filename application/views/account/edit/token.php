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
                    Regenerate API Key
                    <a href="<?php echo site_url('account') ?>" class="right"><i class="fa fa-times-circle action"></i></a>
                </h2>

                <form method="post" action="<?php echo site_url('account/edit/token') ?>">
                    <input type="hidden" name="spk" value="<?php echo $this->config->item('stripe_publishable_key') ?>" />

                    <p><span class="label radius alert">WARNING</span> <strong>This may have unintended consequences and cannot be undone.</strong></p>

                    <p>Are you absolutely sure you want to regenerate your API key?</p>

                    <div class="row">
                        <div class="small-12 medium-8 columns">
                            <hr />
                            <div class="row">
                                <div class="small-6 columns">
                                    <a href="<?php echo site_url('account') ?>">Cancel</a>
                                </div>
                                <div class="small-6 columns form-submit">
                                    <input type="submit" value="Regenerate API Key" class="gi-button" />
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

<script type="text/javascript" src="https://js.stripe.com/v1/"></script>
<?php $this->load->view('components/_footer') ?>
