<?php $this->load->view('components/_header') ?>
<?php $this->load->view('components/_nav') ?>

<?php $this->load->view('campaigns/_nav', array(
    'active' => 'donation'
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
                    Edit Donation
                    <a href="<?php echo site_url('campaigns/'.$campaign->id_token.'/donations/'.$donation->id_token) ?>" class="right"><i class="fa fa-times-circle action"></i></a>
                </h2>

                <form method="post" action="<?php echo site_url('campaigns/'.$campaign->id_token.'/donations/'.($donation->id_token ? $donation->id_token : 'new').'/edit') ?>">
                    <div class="row">
                        <div class="small-12 medium-8 columns">
                            <label for="input-first-name"<?php echo form_error('first_name') ? ' class="error"' : '' ?>>First Name<span class="required">*</span></label>
                            <input type="text" id="input-first-name" value="<?php echo $this->input->post('first_name') ? $this->input->post('first_name') : $donation->first_name ?>" name="first_name"<?php echo form_error('first_name') ? ' class="error"' : '' ?> />
                        </div>
                    </div>

                    <div class="row">
                        <div class="small-12 medium-8 columns">
                            <label for="input-last-name"<?php echo form_error('last_name') ? ' class="error"' : '' ?>>Last Name<span class="required">*</span></label>
                            <input type="text" id="input-last-name" value="<?php echo $this->input->post('last_name') ? $this->input->post('last_name') : $donation->last_name ?>" name="last_name"<?php echo form_error('last_name') ? ' class="error"' : '' ?> />
                        </div>
                    </div>

                    <div class="row">
                        <div class="small-12 medium-8 columns">
                            <label>Donation Date<span class="required">*</span></label>
                            <div class="row">
                                <div class="small-12 medium-6 columns">
                                    <select name="month">
                                    <?php $od = strtotime('01/01/2012'); for($i=0; $i<12; $i++) : ?>
                                        <?php $d = strtotime('+'.$i.' months', $od) ?>
                                        <option value="<?php echo date('F', $d) ?>"<?php if( (!$donation->donation_date && date('F') == date('F', $d)) || ($donation->donation_date && date('F', $d) == date('F', strtotime($donation->donation_date))) ) : ?> selected<?php endif ?>><?php echo date('F', $d) ?></option>
                                    <?php endfor ?>
                                    </select>
                                </div>
                                <div class="small-12 medium-2 columns">
                                    <select name="day">
                                    <?php for($i=1; $i<=31; $i++) : ?>
                                        <option value="<?php echo $i ?>"<?php if( (!$donation->donation_date && date('j') == $i) || ($donation->donation_date && $i == date('j', strtotime($donation->donation_date))) ) : ?> selected<?php endif ?>><?php echo $i ?></option>
                                    <?php endfor ?>
                                    </select>
                                </div>
                                <div class="small-12 medium-4 columns">
                                    <select name="year">
                                    <?php for($i=2010; $i<=date('Y')+1; $i++) : ?>
                                        <option value="<?php echo $i ?>"<?php if( (!$donation->donation_date && date('Y') == $i) || ($donation->donation_date && $i == date('Y', strtotime($donation->donation_date))) ) : ?> selected<?php endif ?>><?php echo $i ?></option>
                                    <?php endfor ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="small-12 medium-4 columns">
                            <?php if( count($campaign->donation_levels) ) : ?>
                            <label>Donation Level</label>
                            <select name="donation_level">
                                <option value="">No level selected</option>
                                <?php foreach( $campaign->donation_levels as $level ) : ?>
                                    <option value="<?php echo $level->level_id ?>"<?php if( $donation && $donation->donation_level_id && $donation->donation_level_id == $level->level_id ) : ?> selected<?php endif ?>><?php echo $level->label ?> (<?php echo $level->amount/100 ?>)</option>
                                <?php endforeach ?>
                            </select>
                            <?php endif ?>

                            <label for="input-amount"<?php echo form_error('amount') ? ' class="error"' : '' ?>>Total Amount<span class="required">*</span></label>
                            <div class="row collapse">
                                <div class="small-2 columns">
                                    <span class="prefix"><?php echo CURRENCY_SYMBOL ?></span>
                                </div>
                                <div class="small-10 columns">
                                    <input type="text" placeholder="0.00" value="<?php echo $this->input->post('amount') ? $this->input->post('amount') : $donation->donation_total/100 ?>" name="amount" id="input-amount"<?php echo form_error('amount') ? ' class="error"' : '' ?> />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="small-12 medium-8 columns">
                            <label for="input-email"<?php echo form_error('email') ? ' class="error"' : '' ?>>Email<span class="required">*</span></label>
                            <input type="text" id="input-email" name="email"<?php echo form_error('email') ? ' class="error"' : '' ?> value="<?php echo $this->input->post('email') ? $this->input->post('email') : $donation->email_address ?>" />
                            <label for="input-contact" class="label-checkbox">
                                <input type="checkbox" value="1"<?php if( (!$donation->id_token && (!count($_POST) || $this->input->post('contact'))) || ($donation->id_token && $donation->contact) ) : ?> checked<?php endif ?> name="contact" id="input-contact" />
                                We have permission to follow-up
                            </label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="small-12 medium-8 columns">
                            <label>Address</label>
                            <input type="text" name="street" value="<?php echo $this->input->post('street') ? $this->input->post('street') : $donation->billing_address1 ?>" placeholder="Street Address" />
                            <div class="row">
                                <div class="small-12 medium-5 columns">
                                    <input type="text" name="city" value="<?php echo $this->input->post('city') ? $this->input->post('city') : $donation->billing_city ?>" placeholder="City" />
                                </div>
                                <div class="small-12 medium-5 columns">
                                    <input type="text" name="state" value="<?php echo $this->input->post('state') ? $this->input->post('state') : $donation->billing_state ?>" placeholder="State" />
                                </div>
                                <div class="small-12 medium-2 columns">
                                    <input type="text" name="zip" value="<?php echo $this->input->post('zip') ? $this->input->post('zip') : $donation->billing_postal_code ?>" placeholder="Zip" />
                                </div>
                            </div>
                            <input type="text" name="country" value="<?php echo $this->input->post('country') ? $this->input->post('country') : $donation->billing_country ?>" placeholder="Country" />
                        </div>
                    </div>

                    <?php if( $campaign->custom_fields ) : ?>
                        <?php
                            $existing = array();
                            if( $donation->custom_responses ) {
                                foreach( $donation->custom_responses as $field ) {
                                    $existing[$field->field_id] = $field->response;
                                }
                            }
                        ?>

                        <div class="row">
                            <div class="small-12 medium-8 columns">
                                <?php foreach( $campaign->custom_fields as $field ) : ?>

                                    <?php if( !$field->status ) { continue; } ?>

                                    <label id="field-<?php echo $field->field_id ?>"><?php echo $field->field_label ?></label>

                                    <?php if( $field->field_type == 'dropdown' ) : ?>
                                        <select id="field-<?php echo $field->field_id ?>" class="six" name="fields[<?php echo $field->field_id ?>]">
                                            <option value="">Select One</option>
                                            <?php foreach( $field->options as $v ) : ?>
                                                <option value="<?php echo $v ?>"<?php if( array_key_exists($field->field_id, $existing) && $existing[$field->field_id] == $v) : ?> selected<?php endif ?>><?php echo $v ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    <?php else : ?>
                                        <input type="text" class="six" id="field-<?php echo $field->field_id ?>" name="fields[<?php echo $field->field_id ?>]" value="<?php if( array_key_exists($field->field_id, $existing) ) : echo $existing[$field->field_id]; endif; ?>" />
                                    <?php endif ?>

                                <?php endforeach ?>
                            </div>
                        </div>
                    <?php endif ?>

                    <div class="row">
                        <div class="small-12 medium-8 columns">
                            <hr />
                            <div class="row">
                                <div class="small-6 columns">
                                    <p class="form-meta"><span class="required">*</span> Denotes required field</p>
                                </div>
                                <div class="small-6 columns form-submit">
                                    <input type="submit" value="Save Donation" class="gi-button" />
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
