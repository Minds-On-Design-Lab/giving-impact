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
                    User Settings
                    <a href="<?php echo site_url('account') ?>" class="right"><i class="fa fa-times-circle action"></i></a>
                </h2>

                <form method="post" action="<?php echo site_url('account/edit/user') ?>">

                    <?php if( validation_errors() ) : ?>
                    <div class="alert-box alert">
                        There were errors saving your information. Please review the form and try again.
                    </div>
                    <?php endif ?>

                    <div class="row">
                        <div class="small-12 medium-8 columns">
                            <label for="input-first-name"<?php echo form_error('first_name') ? ' class="error"' : '' ?>>First Name:</label>
                            <input type="text" name="first_name" id="input-first-name" value="<?php echo $this->input->post('first_name') ? $this->input->post('first_name') : $account->first_name ?>"<?php echo form_error('first_name') ? ' class="error"' : '' ?> />
                        </div>
                    </div>

                    <div class="row">
                        <div class="small-12 medium-8 columns">
                            <label for="input-last-name"<?php echo form_error('last_name') ? ' class="error"' : '' ?>>Last Name:</label>
                            <input type="text" name="last_name" id="input-last-name" value="<?php echo $this->input->post('last_name') ? $this->input->post('last_name') : $account->last_name ?>"<?php echo form_error('last_name') ? ' class="error"' : '' ?> />
                        </div>
                    </div>

                    <div class="row">
                        <div class="small-12 medium-8 columns">
                            <label for="input-email"<?php echo form_error('email') ? ' class="error"' : '' ?>>Email: <span class="required">*</span></label>
                            <input type="text" name="email" id="input-email" value="<?php echo $this->input->post('email') ? $this->input->post('email') : $user->email ?>"<?php echo form_error('email') ? ' class="error"' : '' ?> />
                        </div>
                    </div>

                    <div class="row">
                        <div class="small-12 medium-8 columns">
                            <label for="input-pass"<?php echo form_error('pass') ? ' class="error"' : '' ?>>Change Password: </label>
                            <input type="password" name="pass" id="input-pass" />
                            <?php if( form_error('pass') ) : ?><small class="error">Your passwords didn't match</small><?php endif ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="small-12 medium-8 columns">
                            <label for="input-pass2"<?php echo form_error('pass') ? ' class="error"' : '' ?>>Re-type Password: </label>
                            <input type="password" name="pass2" id="input-pass2" />
                        </div>
                        <div class="four columns"></div>
                    </div>
                    <div class="row">
                        <div class="small-12 medium-8 columns">
                            <label for="input-timezone">Timezone: </label>
                            <select name="timezone" id="input-timezone">
                                <?php foreach( $timezones as $label => $tz ) : ?>
                                    <option value="<?php echo $tz ?>"<?php if( $account->timezone == $tz ) : ?> selected<?php endif ?>><?php echo $label ?></option>
                                <?php endforeach ?>
                            </select>
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
