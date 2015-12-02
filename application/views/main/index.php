<?php $this->load->view('components/_header') ?>

<?php $this->load->view('components/_nav') ?>

<div class="row">
    <div class="small-12 medium-6 columns medium-centered">
        <div class="box">
            <h2>Login</h2>

            <form method="post" action="<?php echo site_url('login') ?>">
                <?php if( $this->session->flashdata('login_message') ) : ?>
                    <div class="alert-box alert">
                        <?php echo $this->session->flashdata('login_message') ?>
                    </div>
                <?php endif ?>
                <label for="input-email">Email</label>
                <input id="input-email" type="text" name="email" />

                <label for="input-password">Password</label>
                <input id="input-password" type="password" name="password" />

                <div class="clearfix">
                    <input type="submit" class="gi-button right" value="Login" />
                    <a href="<?php echo site_url('forgot') ?>">Forgot password?</a>
                </div>

            </form>
        </div>
    </div>
</div>

<?php $this->load->view('components/_footer') ?>
