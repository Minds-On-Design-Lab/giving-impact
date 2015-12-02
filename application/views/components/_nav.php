<div id="primary-nav">
    <div class="contain-to-grid">
        <nav class="top-bar" data-topbar role="navigation">
            <ul class="title-area">
              <li class="name">
                <h1><a href="<?php echo $this->session->userdata('is_authorized') ? site_url('dashboard') : site_url('/') ?>"><img src="<?php echo base_url('assets/images/giving-impact.png'); ?>" alt="Giving Impact"></a></h1>
              </li>
               <!-- Remove the class "menu-icon" to get rid of menu icon. Take out "Menu" to just have icon alone -->
              <li class="toggle-topbar menu-icon"><a href="#"><span>Menu</span></a></li>
            </ul>

            <section class="top-bar-section">
                <ul class="right">
                  <?php if( authorized_user() ) : ?>
                    <li><a href="<?php echo site_url('dashboard') ?>">Dashboard</a></li>
                    <li><a href="<?php echo site_url('account') ?>">Account Settings</a></li>
                  <?php endif ?>
                    <?php if( authorized_user() ) : ?>
                        <?php if( authorized_user()->is_super_admin ) : ?>
                            <li><a href="<?php echo site_url('admin') ?>">Adminstration & Reporting</a></li>
                        <?php endif ?>
                        <li><a href="<?php echo site_url('logout') ?>">Logout</a></li>
                    <?php endif ?>
                </ul>
            </section>
        </nav>
    </div>
</div>
