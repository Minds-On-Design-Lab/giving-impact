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
                    Design &amp; Communication Settings
                    <a href="<?php echo site_url('campaigns/'.$campaign->id_token.'/edit') ?>" class="right"><i class="fa fa-times-circle action"></i></a>
                </h2>

                <form method="post" action="<?php echo site_url('campaigns/'.$campaign->id_token.'/edit/design') ?>" enctype="multipart/form-data">

                    <div class="row">
                        <div class="small-12 medium-8 columns">
                            <label for="input-file">Campaign Image</label>
                            <p class="directions">Add an image to display on your Widget and Campaign Landing Page. Image dimensions should be 300 pixels x 200 pixels. File needs to be less than 100K and a web optimized jpeg, gif or png.</p>

                            <?php if( $campaign->image_url ) : ?>
                                <img src="<?php echo $campaign->thumb_url ?>" id="campaign-image-thumb">
                                <p class="directions">
                                    <a href="#" data-action="remove-image">Remove image</a>
                                </p>
                            <?php endif ?>
                            <input type="file" name="file" id="input-file" />
                        </div>
                    </div>

                    <div class="row">
                        <div class="small-12 medium-8 columns">
                            <label for="input-youtube">YouTube Video</label>
                            <p class="directions">Enter the YouTube Video Id to add a video to your campaign. Will show up in the campaign's hosted checkout page. (Example: http://www.youtube.com/watch?v=<strong>dtdo_pOwuHI</strong>)</p>
                            <div class="row collapse">
                                <div class="small-7 columns">
                                    <span class="prefix">http://www.youtube.com/watch?v=</span>
                                </div>
                                <div class="small-5 columns">
                                    <input type="text" placeholder="######" value="<?php echo $this->input->post('youtube') ? $this->input->post('youtube') : $campaign->youtube_id ?>" name="youtube" id="input-youtube"<?php echo form_error('youtube') ? ' class="error"' : '' ?> />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="small-12 medium-8 columns">
                            <label for="input-analytics-id">Google Analytics Property ID</label>
                            <p class="directions">Adding a Property ID will enable ecommerce tracking for our hosted checkout pages.</p>
                            <input type="text" placeholder="UA-555555-1" value="<?php echo $this->input->post('analytics_id') ? $this->input->post('analytics_id') : $campaign->analytics_id ?>" name="analytics_id" id="input-analytics-id" />
                        </div>
                    </div>

                    <div class="row">
                        <div class="small-12 medium-8 columns">
                            <label>Display Options</label>
                            <p class="directions">The following control the display of donation targets and totals on hosted donation pages and can be used on API driven ones to do the same.</p>
                            <label for="input-display_total" class="label-checkbox">
                                <input type="checkbox" name="display_donation_target" value="1" id="input-display_total"<?php if( $this->input->post('display_donation_target') || $campaign->display_donation_target ) : ?> checked<?php endif ?>/>
                                Display campaign target
                            </label>
                            <label for="input-display_current" class="label-checkbox">
                                <input type="checkbox" name="display_donation_total" value="1" id="input-display_current"<?php if( $this->input->post('display_donation_total') || $campaign->display_donation_total ) : ?> checked<?php endif ?>/>
                                Display campaign total
                            </label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="small-12 medium-8 columns">
                            <hr />
                            <div class="row">
                                <div class="six columns">
                                    <p class="form-meta"><span class="required">*</span> Denotes required field</p>
                                </div>
                                <div class="six columns form-submit">
                                    <input type="submit" value="Save Settings" class="button" />
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
