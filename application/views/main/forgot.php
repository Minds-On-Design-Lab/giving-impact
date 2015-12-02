<?php $this->load->view('components/_header') ?>

<?php $this->load->view('components/_nav') ?>

<div class="row">
    <div class="small-12 medium-6 columns medium-centered">
        <div class="box">
            <h2>Forgot password</h2>

            <form method="post" action="<?php echo site_url('forgot') ?>">
                <?php if( $message ) : ?>
                    <div class="alert-box <?php echo $type ?>">
                        <?php echo $message ?>
                    </div>
                <?php endif ?>

                <p>Please provide the email address registered to your account.</p>

                <label for="input-email">Email</label>
                <input id="input-email" type="text" name="email" />

                <div class="clearfix">
                    <input type="submit" class="button right" value="Reset my password" />
                </div>

            </form>
        </div>

    </div>
</div>

<?php $this->load->view('components/_footer') ?>
