<?php
    $l = 'en_US';
    if( $account->currency ) {
        $lc_denom = array(
            "aud" => "en_AU",
            "eur" => "en_IE",
            "cad" => "en_CA",
            "dkk" => "da_DK",
            "nok" => "nb_NO",
            "sek" => "sv_SE",
            "gbp" => "en_GB",
            "uds" => "en_US"
        );
        if( array_key_exists(strtolower($account->currency), $lc_denom) ) {
            $l = $lc_denom[$account->currency];
        }
    }
    //Set Currency
    setlocale(LC_MONETARY, $l.'.UTF-8');

    define('CURRENCY_SYMBOL', \Symfony\Component\Intl\Intl::getCurrencyBundle()->getCurrencySymbol(strtoupper($account->currency)));

?>
<!DOCTYPE html>

<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
    <meta charset="utf-8" />

    <!-- Set the viewport width to device width for mobile -->
    <meta name="viewport" content="width=device-width" />
    <?php if( isset($show_og) && $show_og ) : ?>
        <meta property="og:title" content="<?php echo $campaign->title ?>" />
        <meta property="og:type" content="cause" />
        <?php if ($account->image_url) : ?>
            <meta property="og:image" content="<?php echo $account->image_url ?>" />
        <?php endif ?>
        <meta property="og:description" content="<?php echo htmlentities(strip_tags($campaign->description)) ?>">
    <?php endif ?>

    <title><?php echo $campaign->title ?> | <?php echo $title_action ?></title>

    <!-- Included CSS Files (Compressed) -->

    <link rel="stylesheet" href="<?php echo base_url('assets/stylesheets/hosted-donation.css') ?>">
    <script>window.gi_base = '<?php echo site_url('/'); ?>';window._gi_req_state = <?php echo ($account->currency == 'usd') ? 'true' : 'false'; ?>;</script>
    <script src="<?php echo base_url('assets/javascripts/gi_top.js') ?>"></script>

    <!-- IE Fix for HTML5 Tags -->
    <!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

</head>
<body>
    <?php if( $title_action == 'Donate' ) : ?>
    <div class="row">
      <div class="small-12 columns">
        <div class="security"><i class="fa fa-lock fa-lg"></i> <strong>Safe and Secure</strong> <span class="security-detail">- All donations made through this page are secure.</span> <img src="<?php echo base_url('assets/images/solid@2x.png'); ?>" alt="Powered by Stripe" class="stripe-logo right"></div>
      </div>
    </div>
    <?php endif ?>
