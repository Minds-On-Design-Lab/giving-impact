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
                    Basic Campaign Settings
                    <?php if ($campaign->id_token) : ?>
                    <a href="<?php echo site_url('campaigns/'.$campaign->id_token.'/edit') ?>" class="right"><i class="fa fa-times-circle action"></i></a>
                    <?php else: ?>
                    <a href="<?php echo site_url('dashboard') ?>" class="right"><i class="fa fa-times action"></i></a>
                    <?php endif ?>
                </h2>

                <form method="post" action="<?php echo $campaign->id_token ? site_url('campaigns/'.$campaign->id_token.'/edit/basic') : site_url('campaigns/new') ?>">

                    <div class="row">
                        <div class="small-12 medium-8 columns">
                            <label for="input-title"<?php echo form_error('title') ? ' class="error"' : '' ?>>Title<span class="required">*</span></label>
                            <input type="text" name="title" id="input-title" value="<?php echo $this->input->post('title') ? $this->input->post('title') : $campaign->title ?>"<?php echo form_error('first_name') ? ' class="error"' : '' ?> />
                        </div>
                    </div>

                     <div class="row">
                        <div class="small-12 medium-8 columns">
                            <label for="input-target"<?php echo form_error('target') ? ' class="error"' : '' ?>>Target<span class="required">*</span></label>
                            <div class="row collapse">
                                <div class="small-2 medium-1 columns">
                                    <span class="prefix"><?php echo CURRENCY_SYMBOL ?></span>
                                </div>
                                <div class="small-10 medium-4 columns">
                                    <input type="text" placeholder="0.00" value="<?php echo $this->input->post('target') ? $this->input->post('target') : $campaign->donation_target/100 ?>" name="target" id="input-target"<?php echo form_error('target') ? ' class="error"' : '' ?> />
                                </div>
                                <div class="medium-7 columns">&nbsp;</div>
                            </div>
                        </div>
                    </div>

                   <div class="row">
                        <div class="small-12 medium-8 columns">
                            <label for="input-minimum"<?php echo form_error('minimum') ? ' class="error"' : '' ?>>Minimum Donation<span class="required">*</span></label>
                             <p class="directions">Must be 5 or more.</p>
                            <div class="row collapse">
                                <div class="small-2 medium-1 columns">
                                    <span class="prefix"><?php echo CURRENCY_SYMBOL ?></span>
                                </div>
                                <div class="small-10 medium-4 columns">
                                    <input type="text" placeholder="0.00" value="<?php echo $this->input->post('minimum') ? $this->input->post('minimum') : ($campaign->donation_minimum ? $campaign->donation_minimum/100 : '5.00') ?>" name="minimum" id="input-minimum"<?php echo form_error('minimum') ? ' class="error"' : '' ?> />
                                </div>
                                <div class="medium-7 columns">&nbsp;</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="small-12 medium-8 columns">
                            <label for="input-description"<?php echo form_error('description') ? ' class="error"' : '' ?>>Description<span class="required">*</span></label>
                            <textarea rows="7" name="description" id="input-description"<?php echo form_error('description') ? ' class="error"' : '' ?>><?php echo $this->input->post('description') ? $this->input->post('description') : $campaign->description ?></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="small-12 medium-8 columns">
                            <label>Donation Options</label>
                            <label for="input-open-input" class="label-checkbox">
                                <input type="radio" name="levels" value="0" id="input-open-input"<?php if( $this->input->post('levels') === 0 || !$campaign->enable_donation_levels ) : ?> checked<?php endif ?> /> Open input - Donor can specify any amount
                            </label>
                            <label for="input-levels-input" class="label-checkbox">
                                <input type="radio" name="levels" value="1" id="input-levels-input"<?php if( $this->input->post('levels') === 1 || $campaign->enable_donation_levels ) : ?> checked<?php endif ?> /> Donation levels - Donor can choose from a list or enter a specific amount
                            </label>

                            <!-- Donation Levels -->
                            <div id="container-donation-levels">
                                <table role="grid">
                                    <thead>
                                        <tr>
                                            <th>Level Amount</th>
                                            <th>Level Description</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <?php if( $campaign->donation_levels || is_array($campaign->donation_levels) ) : ?>
                                        <?php foreach( $campaign->donation_levels as $lvl ) : ?>

                                            <tr>
                                                <td>
                                                    <div class="row collapse">
                                                        <div class="small-2 columns">
                                                            <span class="prefix"><?php echo CURRENCY_SYMBOL ?></span>
                                                        </div>
                                                        <div class="small-10 columns">
                                                            <input type="text" name="level_amounts[]" value="<?php echo $lvl->amount/100 ?>" />
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="text" name="level_labels[]" value="<?php echo $lvl->label ?>" />

                                                    <input type="hidden" name="level_level_ids[]" value="<?php echo $lvl->level_id ?>" />
                                                    <input type="hidden" name="level_statuses[]" value="1"/>
                                                </td>
                                                <td>
                                                    <a href="#" data-action="remove-row"><i class="fa fa-trash fa-lg action"></i></a>
                                                </td>
                                            </tr>
                                        <?php endforeach ?>
                                    <?php endif ?>
                                    <tr>
                                        <td colspan="3">
                                            <a href="#" data-action="add-row" data-template="donation-level-row" class="small radius button">Add Donation Level</a>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="small-12 medium-8 columns">
                            <label>Campaign Type<span class="required">*</span></label>
                            <label for="input-type-single" class="label-checkbox">
                                <input type="radio" name="type" value="0" id="input-type-single"<?php if( $this->input->post('type') === 0 || !$campaign->has_giving_opportunities ) : ?> checked<?php endif ?> /> This is a single giving opportunity campaign
                            </label>
                            <label for="input-type-opps" class="label-checkbox">
                                <input type="radio" name="type" value="1" id="input-type-opps"<?php if( $this->input->post('type') === 1 || $campaign->has_giving_opportunities ) : ?> checked<?php endif ?> /> Allow <span data-tooltip aria-haspopup="true" class="has-tip radius" title="Giving Opportunities are like campaigns within the campaign. Use them for teams, P2P or advocate driven efforts.">multiple giving opportunities</span> to be created within this campaign
                            </label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="small-12 medium-8 columns">
                            <label<?php echo form_error('status') ? ' class="error"' : '' ?>>Campaign Status<span class="required">*</span></label>
                            <label for="input-status-active" class="label-checkbox">
                                <input type="radio" name="status" value="1" id="input-status-active"<?php if( $this->input->post('status') || $campaign->status == 1 || !$campaign->id_token ) : ?> checked<?php endif ?>/>
                                Active and accepting donations
                            </label>
                            <label for="input-status-inactive" class="label-checkbox">
                                <input type="radio" name="status" value="0" id="input-status-inactive"<?php if( !$campaign->status && $campaign->id_token ) : ?> checked<?php endif ?> />
                                Inactive
                            </label>
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
                                    <input type="submit" value="Save Settings" class="button" />
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

<script id="donation-level-row" type="text/x-handlebars-template">
    <tr>
        <td>
            <div class="row collapse">
                <div class="small-2 columns">
                    <span class="prefix"><?php echo CURRENCY_SYMBOL ?></span>
                </div>
                <div class="small-10 columns">
                    <input type="text" name="level_amounts[]" value="" />
                </div>
            </div>
        </td>
        <td>
            <input type="text" name="level_labels[]" value="" />
            <input type="hidden" name="level_level_ids[]" value="" />
        </td>
        <td>
            <a href="#" data-action="remove-row"><i class="general foundicon-trash action"></i></a>
        </td>
    </tr>
</script>


<?php $this->load->view('components/_footer') ?>
