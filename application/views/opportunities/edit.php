<?php $this->load->view('components/_header') ?>
<?php $this->load->view('components/_nav') ?>


<?php if( $opportunity && $opportunity->id_token ) : ?>
    <?php $this->load->view('opportunities/_nav', array(
        'active' => false
    )) ?>
<?php else : ?>
    <?php $this->load->view('campaigns/_nav', array(
        'active' => false
    )) ?>
<?php endif ?>

<div id="page">
    <div id="page-header">
        <div class="row">
            <div class="small-12 columns">
                <h1 class="page-title"><?php echo ($opportunity && $opportunity->id_token) ? $opportunity->title : $campaign->title; ?></h1>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="small-12 medium-9 columns">
            <div class="box">
                <h2>
                    <?php if ($opportunity && $opportunity->id_token) {
                        echo 'Edit Giving Opportunity <a href="'.site_url("campaigns/$campaign->id_token/opportunities/$opportunity->id_token").'" class="right"><i class="fa fa-times-circle action"></i></a>';
                    } else {
                        echo 'New Giving Opportunity <a href="'.site_url("campaigns/$campaign->id_token/opportunities").'" class="right"><i class="fa fa-times-circle action"></i></a>';
                    }
                    ?>

                </h2>

                <form method="post" action="<?php echo $opportunity->id_token ? site_url('campaigns/'.$campaign->id_token.'/opportunities/'.$opportunity->id_token.'/edit') : site_url('campaigns/'.$campaign->id_token.'/opportunities/new') ?>" enctype="multipart/form-data">


                    <div class="row">
                        <div class="small-12 medium-8 columns">
                            <label for="input-title"<?php echo form_error('title') ? ' class="error"' : '' ?>>Opportunity Name<span class="required">*</span></label>
                            <input type="text" id="input-title" name="title" value="<?php echo $this->input->post('title') ? $this->input->post('title') : $opportunity->title ?>" <?php echo form_error('title') ? ' class="error"' : '' ?>/>
                        </div>
                    </div>

                    <div class="row">
                        <div class="small-12 medium-8 columns">
                            <label for="input-target"<?php echo form_error('target') ? ' class="error"' : '' ?>>Target</label>
                            <div class="row collapse">
                                <div class="small-1 columns">
                                    <span class="prefix"><?php echo CURRENCY_SYMBOL ?></span>
                                </div>
                                <div class="small-11 medium-4 columns">
                                    <input type="text" placeholder="0.00" value="<?php echo $this->input->post('target') ? $this->input->post('target') : $opportunity->donation_target/100 ?>" name="target" id="input-target"<?php echo form_error('target') ? ' class="error"' : '' ?> />
                                </div>
                                <div class="medium-5 columns">&nbsp;</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="small-12 medium-8 columns">
                            <label for="input-description"<?php echo form_error('description') ? ' class="error"' : '' ?>>Description<span class="required">*</span></label>
                            <textarea rows="5" cols="40" name="description" id="input-description"<?php echo form_error('description') ? ' class="error"' : '' ?>><?php echo $this->input->post('description') ? $this->input->post('description') : $opportunity->description ?></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="small-12 medium-8 columns">
                            <label for="input-file">Image</label>

                            <p class="directions">Add an image to display on your hosted checkout page or to be available via API. Image dimensions should be 300 pixels x 200 pixels exactly. File needs to be less than 100K and a web optimized jpeg, gif or png.</p>

                            <?php if( $opportunity->image_url ) : ?>
                                <img src="<?php echo $opportunity->thumb_url ?>" id="campaign-image-thumb">
                                <p class="directions">
                                    <a href="#" data-action="remove-image">Remove image</a>
                                </p>
                            <?php endif ?>
                            <input type="file" name="file" />
                        </div>
                    </div>

                    <div class="row">
                        <div class="small-12 medium-8 columns">
                            <label for="input-youtube">YouTube Video</label>
                            <div class="row collapse">
                                <div class="small-7 columns">
                                    <span class="prefix">http://www.youtube.com/watch?v=</span>
                                </div>
                                <div class="small-5 columns">
                                    <input type="text" placeholder="XXXXXXX" value="<?php echo $this->input->post('youtube') ? $this->input->post('youtube') : $opportunity->youtube_id ?>" name="youtube" id="input-youtube"<?php echo form_error('youtube') ? ' class="error"' : '' ?> />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="small-12 medium-8 columns">
                            <label<?php echo form_error('status') ? ' class="error"' : '' ?>>Giving Opportunity Status<span class="required">*</span></label>
                            <label for="input-status-active" class="option">
                                <input type="radio" name="status" value="1" id="input-status-active"<?php if( $this->input->post('status') || !$opportunity->id_token || $opportunity->status ) : ?> checked<?php endif ?>/>
                                Active and accepting donations
                            </label>
                            <label for="input-status-inactive" class="option">
                                <input type="radio" name="status" value="0" id="input-status-inactive"<?php if( $opportunity->id_token && !$opportunity->status ) : ?> checked<?php endif ?> />
                                Inactive
                            </label>
                        </div>
                    </div>

                <?php if( $campaign->campaign_fields ) : ?>
                    <?php
                        $responses = json_decode(json_encode($opportunity->campaign_responses), true);
                        $out = array();
                        if( $responses && count($responses) ) {
                            foreach( $responses as $r ) {
                                $out[$r['campaign_field_id']] = $r;
                            }
                        }
                        $responses = $out;
                    ?>

                    <?php foreach( $campaign->campaign_fields as $field ) : ?>
                        <?php if( !$field->status ) { continue; } ?>
                        <?php
                            $existing = false;
                            if( $responses && array_key_exists($field->field_id, $responses) ) {
                                $existing = $responses[$field->field_id];
                            }
                        ?>
                        <div class="row">
                            <div class="small-12 medium-8 columns">
                                <label id="field-<?php echo $field->field_id ?>"><?php echo $field->field_label ?><?php if( $field->required ) : ?><span class="required">*</span><?php endif ?></label>
                                <?php if( $field->field_type == 'dropdown' ) : ?>
                                    <select id="field-<?php echo $field->field_id ?>" class="six" name="fields[<?php echo $field->field_id ?>]">
                                        <option value="">Select One</option>
                                        <?php foreach( $field->options as $v ) : ?>
                                            <option value="<?php echo $v ?>"<?php echo ($existing && $existing['response'] == $v) ? ' selected' : '' ?>><?php echo $v ?></option>
                                        <?php endforeach ?>
                                    </select>
                                <?php else : ?>
                                    <input type="text" class="six" id="field-<?php echo $field->field_id ?>" name="fields[<?php echo $field->field_id ?>]" value="<?php echo ($existing && $existing['response']) ? $existing['response'] : '' ?>" />
                                <?php endif ?>
                            </div>
                        </div>
                    <?php endforeach ?>
                <?php endif ?>

                        <div class="row">
                            <div class="eight columns mobile-four">
                                <hr />
                                <div class="row">
                                    <div class="six columns">
                                        <p class="form-meta"><span class="required">*</span> Denotes required field</p>
                                    </div>
                                    <div class="six columns form-submit">
                                        <input type="submit" value="Save Giving Opportunity" class="button" />
                                    </div>
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
</div>

<?php $this->load->view('components/_footer') ?>
