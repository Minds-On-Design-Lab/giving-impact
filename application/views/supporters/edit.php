<?php $this->load->view('components/_header') ?>
<?php $this->load->view('components/_nav') ?>

<?php $this->load->view('dashboard/_nav', array(
    'active' => 'supporters'
)) ?>

<div id="page">

    <div id="page-header">
        <div class="row">
            <div class="small-12 columns">
                <h1 class="page-title"><?php echo $supporter->first_name ?> <?php echo $supporter->last_name ?></h1>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="small-12 medium-9 columns">
            <div class="box">
                <h2>
                    Edit Supporter
                    <a href="<?php echo site_url('supporters/'.$supporter->id_token) ?>" class="right"><i class="fa fa-times-circle action"></i></a>
                </h2>

                <form method="post" action="<?php echo site_url('supporters/'.$supporter->id_token.'/edit') ?>">

                    <!-- first_name -->

                    <div class="row">
                        <div class="small-12 medium-8 columns">
                            <label for="input-first-name"<?php echo form_error('first_name') ? ' class="error"' : '' ?>>First Name<span class="required">*</span></label>
                            <input type="text" id="input-first-name" value="<?php echo $this->input->post('first_name') ? $this->input->post('first_name') : $supporter->first_name ?>" name="first_name"<?php echo form_error('first_name') ? ' class="error"' : '' ?> />
                        </div>
                    </div>

                    <!-- last_name -->

                    <div class="row">
                        <div class="small-12 medium-8 columns">
                            <label for="input-last-name"<?php echo form_error('last_name') ? ' class="error"' : '' ?>>Last Name<span class="required">*</span></label>
                            <input type="text" id="input-last-name" value="<?php echo $this->input->post('last_name') ? $this->input->post('last_name') : $supporter->last_name ?>" name="last_name"<?php echo form_error('last_name') ? ' class="error"' : '' ?> />
                        </div>
                    </div>

                    <!-- email -->

                    <div class="row">
                        <div class="small-12 medium-8 columns">
                            <label for="input-email">Email</label>
                            <input type="text" id="input-email" name="email"<?php echo form_error('email') ? ' class="error"' : '' ?> value="<?php echo $this->input->post('email') ? $this->input->post('email') : $supporter->email_address ?>" />
                        </div>
                    </div>

                    <div class="row">
                        <div class="small-12 medium-8 columns">
                            <label>Address</label>
                            <input type="text" name="street_address" value="<?php echo $this->input->post('street_address') ? $this->input->post('street_address') : $supporter->street_address ?>" placeholder="Street Address" />
                            <div class="row">
                                <div class="small-12 medium-5 columns">
                                    <input type="text" name="city" value="<?php echo $this->input->post('city') ? $this->input->post('city') : $supporter->city ?>" placeholder="City" />
                                </div>
                                <div class="small-12 medium-5 columns">
                                    <input type="text" name="state" value="<?php echo $this->input->post('state') ? $this->input->post('state') : $supporter->state ?>" placeholder="State" />
                                </div>
                                <div class="small-12 medium-2 columns">
                                    <input type="text" name="postal_code" value="<?php echo $this->input->post('postal_code') ? $this->input->post('postal_code') : $supporter->postal_code ?>" placeholder="Postal Code" />
                                </div>
                            </div>
                            <input type="text" name="country" value="<?php echo $this->input->post('country') ? $this->input->post('country') : $supporter->country ?>" placeholder="Country" />
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
                                    <input type="submit" value="Save Supporter" class="button" />
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div> <!-- eo box -->
        </div>
        <div class="small-12 medium-3 columns">
            &nbsp;
        </div>
    </div>
</div> <!-- eo page -->

<?php $this->load->view('components/_footer') ?>
