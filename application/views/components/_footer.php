    <footer class="row">
        <div class="small-12 columns">
            <p>&copy;<?php echo Date('Y') ?> Giving Impact&trade; | A <a href="http://mod-lab.com">Minds On Design Lab</a> Project | Licensed under <a href="http://opensource.org/licenses/MIT">The MIT License (MIT)</a> | <a href="<?php echo site_url('diagnostics') ?>">Diagnostics</a>
            <p>Version: <?php echo _GI_VER ?></p>
        </div>
    </footer>

    <script src="<?php echo base_url('assets/javascripts/gi_bottom.js') ?>"></script>
    <script src="<?php echo base_url('assets/javascripts/handlebars.js') ?>"></script>
    <script src="<?php echo base_url('assets/javascripts/jquery.miniColors.min.js') ?>"></script>
    <script src="<?php echo base_url('assets/javascripts/jquery.payment.js') ?>"></script>
    <script src="<?php echo base_url('assets/javascripts/moment.js') ?>"></script>


    <?php if( $this->session->flashdata('message') ) : ?>
        <div class="alert-box <?php echo $this->session->flashdata('message_type') ? $this->session->flashdata('message_type') : 'success' ?>" id="main-message">
            <?php echo $this->session->flashdata('message') ?>
            <!-- <a href="" class="close"><i class="fa fa-times-circle action"></i></a> -->
        </div>
        <script>
            $('#main-message').css({
                'position': 'fixed'
            });
            $('#main-message').css({
                'top': -3+$('nav.top-bar').height()+'px',
                'left': (($(window).width()/2) - ($('#main-message').width()/2))+'px'
            });
            $('#main-message').click(function() {
                $(this).fadeOut();
            });
            setTimeout(function() {
                $('#main-message').fadeOut();
            }, 5000);
        </script>
    <?php endif ?>
    <div id="scratch"></div>
</body>
</html>
