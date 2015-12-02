<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="initial-scale=1.0">    <!-- So that mobile webkit will display zoomed in -->
    <meta name="format-detection" content="telephone=no"> <!-- disable auto telephone linking in iOS -->

    <title>Antwort - responsive Email Layout</title>
    <style type="text/css">

        /* Resets: see reset.css for details */
        .ReadMsgBody { width: 100%; background-color: #4b4c4c;}
        .ExternalClass {width: 100%; background-color: #4b4c4c;}
        .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height:100%;}
        body {-webkit-text-size-adjust:none; -ms-text-size-adjust:none;}
        body {margin:0; padding:0;}
        table {border-spacing:0;}
        table td {border-collapse:collapse;}
        .yshortcuts a {border-bottom: none !important;}


        /* Constrain email width for small screens */
        @media screen and (max-width: 600px) {
            table[class="container"] {
                width: 95% !important;
            }
        }

        /* Give content more room on mobile */
        @media screen and (max-width: 480px) {
            td[class="container-padding"] {
                padding-left: 12px !important;
                padding-right: 12px !important;
            }
         }

    </style>
</head>
<body style="margin:0; padding-top:10px; padding-bottom: 10px; padding-left: 0; padding-right: 0; background-color: #4b4c4c;" bgcolor="#4b4c4c" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">

<br>

<!-- 100% wrapper (colored background) -->
<table border="0" width="100%" height="100%" cellpadding="0" cellspacing="0" bgcolor="#4b4c4c">
  <tr>
  	<td align="center" valign="top" bgcolor="#4b4c4c" style="background-color: #4b4c4c;">
  		<table border="0" width="600" cellpadding="0" cellspacing="0" class="container">
  			<tr>
          		<td style="padding-bottom: 10px;">
  					<a href="http://givingimpact.com"><img src="<?php echo base_url('assets/images/giving-impact.gif'); ?>" alt="Giving Impact"></a>
  				</td>
  			</tr>
  		</table>
  	</td>
  </tr>
  <tr>
    <td align="center" valign="top" bgcolor="#4b4c4c" style="background-color: #4b4c4c;">
    	<!-- 600px container (white background) -->
      	<table border="0" width="600" cellpadding="0" cellspacing="0" class="container" bgcolor="#ffffff">
        	<tr>
          		<td class="container-padding" bgcolor="#ffffff" style="background-color: #ffffff; padding-left: 30px; padding-right: 30px; font-size: 14px; line-height: 20px; font-family: Helvetica, arial, sans-serif; color: #333;">

