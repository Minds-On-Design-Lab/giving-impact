<?php $this->load->view('components/_external_header', array( 'title_action' => 'Donate' )) ?>
    <div class="row main">

        <div class="small-12 medium-7 medium-push-5 columns">
          <div class="row">
            <header>
              <div class="small-12 columns">
                <div class="logo">
                  <?php if ($account->image_url) : ?><img src="<?php echo $account->image_url ?>" class="org-logo" /><?php endif ?>
                </div>
                <h1>
                  You are donating to <strong><?php echo $campaign->title ?></strong>.
                </h1>
              </div>
            </header>
          </div>

          <?php if( empty($campaign->status) === true || ($campaign->campaign && !$campaign->campaign->status) ) : ?>
            <div class="box donation">
              <p class="inactive-alert">This campaign is not currently accepting donations.</p>
            </div>
          <?php else : ?>
            <form method="post" action="<?php echo site_url('donate/'.$hash.'/checkout') ?>?utm_source=<?php echo $this->input->get('utm_source') ? $this->input->get('utm_source') : 'direct' ?>&utm_medium=<?php echo $this->input->get('utm_medium') ? $this->input->get('utm_medium') : 'link' ?>&utm_campaign=<?php echo $campaign->campaign ? $campaign->campaign->title : $campaign->title ?>" id="form-donation-donate">
            <div class="box donation clearfix<?php if ($campaign->enable_donation_levels) { echo ' box-donation-levels'; }?>">
              <h4>Donation Amount</h4>
              <p>
                Donation must be made in US dollars. Minimum donation is <?php echo money_format('%n', $campaign->minimum_donation_amount/100) ?>.
              </p>
              <?php if( $this->session->flashdata('error') ) : ?>
                <small class="error"><?php echo $this->session->flashdata('error') ?></small>
              <?php endif ?>

              <?php if( !$campaign->enable_donation_levels ) : ?>
                <div class="row">
                  <div class="small-6 columns">
                    <div class="row collapse">
                      <div class="small-2 columns">
                        <span class="prefix"><?php echo CURRENCY_SYMBOL ?></span>
                      </div>
                      <div class="small-6 columns">
                        <input type="text" name="donation_amount" value="<?php echo $this->session->flashdata('amt') ? $this->session->flashdata('amt') : '' ?>" class="input-text" />
                      </div>
                      <div class="small-2 columns end">
                        <span class="postfix">.00</span>
                      </div>
                    </div>
                  </div>
                </div>
              <?php else : ?>
                <?php foreach( $campaign->donation_levels as $level ) : ?>
                  <?php if( !$level['status'] ) { continue; } ?>
                  <label id="level-<?php echo $level['level_id'] ?>">
                    <input type="radio" name="donation_level" value="<?php echo $level['level_id'] ?>" <?php if( ($this->session->flashdata('level') && $this->session->flashdata('level') == $level['level_id']) || ($this->input->post('donation_level') && $this->input->post('donation_level') == $level['level_id']) ) : ?> checked<?php endif ?> />
                      <?php echo money_format('%n', $level['amount']/100) ?> - <?php echo $level['label'] ?>
                    </label>
                    <?php if( $this->session->flashdata('level') && $this->session->flashdata('level') == $level['level_id']) : ?>
                      <div class="row additional_donation_container">
                        <div class="small-12 columns">
                          <div class="row collapse">
                            <div class="small-1 columns">
                              <span class="prefix"><?php echo CURRENCY_SYMBOL ?></span>
                            </div>
                            <div class="small-6 columns end">
                              <input type="text" name="additional_donation" value="<?php echo $this->session->flashdata('addtl') ? $this->session->flashdata('addtl') : '' ?>" />
                            </div>
                          </div>
                          <div class="row collapse">
                            <div class="small-12 columns">
                              <small>Use this field to make an additional donation</small>
                            </div>
                          </div>
                        </div>
                      </div>
                    <?php endif ?>
                  <?php endforeach ?>
                  <label id="level-open">
                    <input type="radio" name="donation_level" value="open" <?php if( !$this->session->flashdata('level') ) : ?> checked<?php endif ?> />
                      Other Amount:
                    </label>
                    <?php if( !$this->session->flashdata('level') ) : ?>
                      <div class="row additional_donation_container">
                        <div class="small-12 columns">
                          <div class="row collapse">
                            <div class="small-1 columns">
                              <span class="prefix"><?php echo CURRENCY_SYMBOL ?></span>
                            </div>
                            <div class="small-6 columns end">
                              <input type="text" name="additional_donation" value="<?php echo $this->session->flashdata('addtl') ? $this->session->flashdata('addtl') : '' ?>" />
                            </div>
                          </div>
                          <div class="row collapse">
                            <div class="small-12 columns">
                              <small>Use this field to make an additional donation</small>
                            </div>
                          </div>
                        </div>
                      </div>
                    <?php endif ?>

                  <?php endif ?>

                  <?php if( $campaign->custom_fields ) : ?>
                    <div class="row">
                      <div class="small-12 columns">
                        <?php
                        $existing = array();
                        if( $this->session->flashdata('fields') ) {
                          $existing = unserialize($this->session->flashdata('fields'));
                        }
                        ?>
                          <!-- errors -->
                        <?php if( $this->session->flashdata('customerror') ) : ?>
                          <hr>
                          <div class="row">
                            <div class="small-12 columns">
                              <small class="error">
                                <?php echo $this->session->flashdata('customerror') ?>
                              </small>
                            </div>
                          </div>
                        <?php endif ?>
                        <?php foreach( $campaign->custom_fields as $field ) : ?>
                          <hr>

                          <?php if( !$field['status'] ) { continue; } ?>
                          <label class="donation-field" id="field-<?php echo $field['field_id'] ?>"><?php echo $field['field_label'] ?><?php if ($field['required'] == '1') : ?>*<?php endif ?></label>
                            <?php if( $field['field_type'] == 'dropdown' ) : ?>
                              <select id="field-<?php echo $field['field_id'] ?>" class="six" name="fields[<?php echo $field['field_id'] ?>]">
                                <option value="">Select One</option>
                                <?php foreach( $field['options'] as $v ) : ?>
                                  <option value="<?php echo $v ?>"<?php if( array_key_exists($field['field_id'], $existing) && $existing[$field['field_id']] && $v == $existing[$field['field_id']] ) : ?> selected<?php endif ?>><?php echo $v ?></option>
                                  <?php endforeach ?>
                                </select>
                              <?php else : ?>
                                <input type="text" class="six" id="field-<?php echo $field['field_id'] ?>" name="fields[<?php echo $field['field_id'] ?>]" value="<?php echo array_key_exists($field['field_id'], $existing) ? $existing[$field['field_id']] : '' ?>" />
                              <?php endif ?>
                            <?php endforeach ?>
                          </div>
                        </div>
                      <?php endif ?>
                      <hr>
                      <div class="row">
                        <div class="small-12 columns right">
                          <input type="submit" value="Checkout" />
                        </div>
                      </div>
                    </div> <!-- eo box -->
                  <?php endif ?>
                </div>


                <div class="small-12 medium-5 medium-pull-7 columns">
                  <div class="box campaign">
                    <div class="row">
                      <?php if ($campaign->youtube || $campaign->image_url) : ?>
                        <div class="small-12 columns">
                          <?php if( $campaign->youtube ) : ?>
                            <div class="campaign_video flex-video">
                              <iframe src="//www.youtube.com/embed/<?php echo $campaign->youtube ?>" frameborder="0" allowfullscreen></iframe>
                            </div>
                          <?php elseif( $campaign->image_url ) : ?>
                            <img src="<?php echo $campaign->image_url ?>" class="campaign-thumb"/>
                          <?php endif ?>
                        </div>
                      <?php endif ?>
                      <div class="small-12 columns">
                        <div class="description">
                          <?php echo $this->typography->auto_typography($campaign->description) ?>
                        </div>
                        <ul class="unstyled">
                          <?php if( ($campaign->campaign && $campaign->campaign->display_donation_target) || (!$campaign->campaign && $campaign->display_donation_target) ) : ?>
                          <li>
                              Goal:
                              <strong><?php echo money_format('%n', $campaign->target/100) ?></strong>
                          </li>
                          <?php endif ?>
                          <?php if( ($campaign->campaign && $campaign->campaign->display_donation_total) || (!$campaign->campaign && $campaign->display_donation_total) ) : ?>
                          <li>
                              Current Total:
                              <strong><?php echo money_format('%n', $campaign->current/100) ?></strong>
                          </li>
                          <?php endif ?>
                        </ul>
                      </div>
                    </div><!-- eo row -->
                  </div>
                </div>
    </div>

</form>
<?php $this->load->view('components/_external_footer') ?>
