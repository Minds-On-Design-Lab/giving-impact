    <script src="<?php echo base_url('assets/javascripts/gi_bottom.js') ?>"></script>
    <script src="<?php echo base_url('assets/javascripts/handlebars.js') ?>"></script>
    <script src="<?php echo base_url('assets/javascripts/jquery.payment.js') ?>"></script>
    <script src="<?php echo base_url('assets/javascripts/moment.js') ?>"></script>

    <div id="scratch"></div>

    <?php if( (!$campaign->campaign && $campaign->analytics_id) || ($campaign->campaign && $campaign->campaign->analytics_id) ) : ?>
        <script type="text/javascript">
            <?php
                $aid = false;
                if( $campaign->campaign && $campaign->campaign->analytics_id ) {
                    $aid = $campaign->campaign->analytics_id;
                } elseif( $campaign->analytics_id ) {
                    $aid = $campaign->analytics_id;
                }
            ?>
            _gaq.push(['_setAccount', '<?php echo $aid ?>']);
            _gaq.push(['_setDomainName', '<?php echo $this->config->item('gi_base_url') ?>']);
            _gaq.push(['_setAllowLinker', true]);
            _gaq.push(['_trackPageview']);

            <?php if( isset($track_trans) && $track_trans ) : ?>
                <?php
                $c = $track_trans['campaign'];
                $d = $track_trans['donation'];
                $a = $track_trans['account'];
                ?>

                _gaq.push(['_addTrans',
                    '<?php echo $d->donation_token ?>',
                    '<?php echo $a->account_name ?>',
                    '<?php echo $d->amount/100 ?>',
                    '',
                    '',
                    '<?php echo $d->billing_city ?>',
                    '<?php echo $d->billing_state ?>',
                    '<?php echo $d->billing_country ?>'
                ]);
                _gaq.push(['_addItem',
                    '<?php echo $d->donation_token ?>',
                    '<?php echo $c->campaign_id ? $c->campaign->campaign_token : $c->campaign_token ?>-<?php echo $c->campaign_id ? $c->campaign->title : $c->title ?>',
                    '<?php echo $c->enable_donation_levels ? 'Level Donation' : 'Open Donation' ?>',
                    '<?php echo $c->enable_donation_levels ? $d->description : 'Donor Defined' ?>',
                    '<?php echo $d->amount/100 ?>',
                    '1'
                ]);

                _gaq.push(['_trackTrans']);
            <?php endif ?>

            (function() {
                var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
            })();

        </script>
    <?php endif ?>

</body>
</html>
