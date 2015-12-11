<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?php echo $campaign->title ?> | <?php echo $campaign->campaign ? $campaign->campaign->email_org_name : $campaign->email_org_name ?></title>
    </head>
    <body bgcolor="white" style="margin: 0 auto; color: #4c4c4d;">
        <table width="100%" bgcolor="white" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td align="center">
                    <table width="650" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td align="center">
                                <table width="600" cellpadding="0" cellspacing="0" border="0">
                                    <?php if( $account->thumb_url ) : ?>
                                    <tr>
                                        <td align="right" style="padding-top:10px; padding-bottom: 10px;"><img src="<?php echo $account->thumb_url ?>" /></td>
                                    </tr>
                                    <?php endif ?>
                                    <tr>
                                        <td align="left" bgcolor="<?php echo $campaign->campaign ? $campaign->campaign->campaign_color : $campaign->campaign_color ?>"><h1 style="padding-left: 20px;font-size: 20px; line-height: 22px; color:<?php echo $campaign->campaign ? $campaign->campaign->header_font : $campaign->header_font ?>;font-family: arial, sans-serif;"><?php echo $campaign->campaign ? $campaign->campaign->title : $campaign->title ?></h1></td>
                                    </tr>
                                </table>
                                <table width="560" cellpadding="0" cellspacing="0" border="0" style="margin-top:30px;">
                                    <tr>
                                        <td style="color: #4c4c4d; font-family: arial, sans-serif; font-size: 14px; line-height: 18px;">
                                            <?php if( $campaign->campaign ) : ?>
                                                <p><strong style="text-weight: bold"><?php echo date('F d, Y', strtotime($donation->donation_date)) ?><br/><br/><?php echo $campaign->campaign->email_org_name ?></strong><br />
                                                <?php echo $campaign->campaign->street_address ?><br />
                                                <?php if( $campaign->campaign->street_address_2 ) : ?>
                                                    <?php echo $campaign->campaign->street_address ?><br />
                                                <?php endif ?>
                                                <?php echo $campaign->campaign->city ?>, <?php echo $campaign->campaign->state ?> <?php echo $campaign->campaign->postal_code ?>
                                                <?php if( $campaign->campaign->country ) : ?><br /><?php echo $campaign->campaign->country ?><?php endif ?></p>
                                            <?php else : ?>
                                                <p><strong style="text-weight: bold"><?php echo date('F d, Y', strtotime($donation->donation_date)) ?><br/><br/><?php echo $campaign->email_org_name ?></strong><br />
                                                <?php echo $campaign->street_address ?><br />
                                                <?php if( $campaign->street_address_2 ) : ?>
                                                    <?php echo $campaign->street_address_2 ?><br />
                                                <?php endif ?>
                                                <?php echo $campaign->city ?>, <?php echo $campaign->state ?> <?php echo $campaign->postal_code ?>
                                                <?php if( $campaign->country ) : ?><br /><?php echo $campaign->country ?><?php endif ?></p>
                                            <?php endif ?>

                                            <p style="margin-top: 25px;"><?php echo $donation->first_name ?> <?php echo $donation->last_name ?>,</p>

                                            <p><strong style="text-weight: bold">Your recurring <?php echo money_format('%n', $donation->amount/100) ?> <?php if( $donation->description ) : ?><?php echo $donation->description ?> <?php endif ?>donation to <?php echo $campaign->campaign ? $campaign->campaign->title : $campaign->title ?> has been canceled.</strong></p>
                                            <p>Please contact us with any questions or followups.</p>
                                        </td>
                                    </tr>
                                </table>
                                <table width="600" cellpadding="0" cellspacing="0" border="0" style="margin-top:10px;">
                                    <tr>
                                        <td align="right" style="height: 5px;" bgcolor="<?php echo $campaign->campaign ? $campaign->campaign->campaign_color : $campaign->campaign_color ?>"><img src="/images/spacer.gif" alt="spacer" width="1" height="1" /></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>