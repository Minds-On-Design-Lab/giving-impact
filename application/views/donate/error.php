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

          <div class="box donation">
            <p class="inactive-alert">This campaign is not currently accepting donations.</p>
          </div>
        </div>
    </div>
<?php $this->load->view('components/_external_footer') ?>
