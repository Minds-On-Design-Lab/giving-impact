<?php
    $l = 'en_US';
    if( $this->auth->authorized_user() && $this->auth->authorized_user()->account->currency ) {
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
        if( array_key_exists(strtolower($this->auth->authorized_user()->account->currency), $lc_denom) ) {
            $l = $lc_denom[$this->auth->authorized_user()->account->currency];
        }
        define('CURRENCY_SYMBOL', \Symfony\Component\Intl\Intl::getCurrencyBundle()->getCurrencySymbol(strtoupper($this->auth->authorized_user()->account->currency)));
    } else {
        define('CURRENCY_SYMBOL', \Symfony\Component\Intl\Intl::getCurrencyBundle()->getCurrencySymbol('USD'));
    }
    //Set Currency
    setlocale(LC_MONETARY, $l.'.UTF-8');
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

    <title>Giving Impact</title>

    <!-- Included CSS Files (Compressed) -->
    <link rel="stylesheet" href="<?php echo base_url('assets/stylesheets/master.css') ?>">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="<?php echo base_url('assets/stylesheets/jquery.miniColors.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/stylesheets/xcharts.css') ?>">

    <script src="<?php echo base_url('assets/javascripts/gi_top.js') ?>"></script>

    <!-- IE Fix for HTML5 Tags -->
    <!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body>
    <?php if( $this->auth->authorized_user() && !$this->auth->authorized_user()->account->currency ) : ?>
    <div class="currency-alert"><strong>Important!</strong> You must <a href="<?php echo site_url('account') ?>#select-currency">select a currency</a> before you can begin accepting donations.</div>
    <?php endif ?>
