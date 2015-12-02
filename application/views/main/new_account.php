<?php $this->load->view('components/_header') ?>

<?php $this->load->view('components/_nav') ?>

<div class="row">
    <div class="small-12 medium-9 columns">
        <div class="box">
            <h2>Create New Account</h2>

            <form method="post" action="<?php echo site_url('new_account') ?>" id="form-signup">

                <?php if( $form_error ) : ?>
                    <br />
                    <small class="error">
                        <?php echo $form_error ?>
                    </small>
                <?php endif ?>

                <label>Your Name<span class="required">*</span></label>
                <div class="row">
                    <div class="six columns">
                        <input type="text" class="text-input" name="first_name" placeholder="First Name" value="<?php echo $this->input->post('first_name') ?>" />
                        <small class="error hide" data-field="first_name">Please provide your first name</small>
                    </div>
                    <div class="six columns">
                        <input type="text" class="text-input" name="last_name" placeholder="Last Name" value="<?php echo $this->input->post('last_name') ?>" />
                        <small class="error hide" data-field="last_name">Please provide your last name</small>
                    </div>
                </div>

                <label for="input-email">Your Email Address<span class="required">*</span></label>
                <input type="text" class="text-input" name="email" placeholder="you@yours.com" id="input-email" value="<?php echo $this->input->post('email') ?>" />
                <small class="error hide" data-field="email">Please provide a valid email address</small>

                <label>Password<span class="required">*</span></label>
                <div class="row">
                    <div class="six columns">
                        <input type="password" class="text-input" name="pass" placeholder="Password" />
                    </div>
                    <div class="six columns">
                        <input type="password" class="text-input" name="pass2" placeholder="Please confirm" />
                        <small class="error hide">Your passwords do not match</small>
                    </div>
                </div>

                <div class="row">
                    <div class="small-12 columns">
                        <hr />
                        <div class="row">
                            <div class="small-6 columns">
                                <p class="form-meta"><span class="required">*</span> Denotes required field</p>
                            </div>
                            <div class="small-6 form-submit">
                                <input type="submit" value="Create Account" class="button" />
                            </div>
                        </div>
                    </div>
                </div>
           </form>
        </div>

    </div>
</div>

<?php $this->load->view('components/_footer') ?>
