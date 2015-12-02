<?php $this->load->view('components/_header') ?>
<?php $this->load->view('components/_nav') ?>

<?php $this->load->view('dashboard/_nav', array(
    'active' => 'campaigns'
)) ?>

<div id="page">
    <div id="page-header">
        <div class="row">
            <div class="small-12 columns">
                <h1 class="page-title">Site Diagnostics</h1>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="small-12 medium-9 columns">
            <div class="box">
                <table role="grid">
                    <tbody>
                        <tr>
                            <td colspan="2"><strong>General</strong></td>
                        </tr>
                        <tr>
                            <td>Site Base URL</td>
                            <td><?php echo $this->config->item('gi_base_url') ?></td>
                        </tr>
                        <tr>
                            <td>GI Version</td>
                            <td>Giving Impact <?php echo _GI_VER ?></td>
                        </tr>
                        <tr>
                            <td>PHP Version</td>
                            <td><?php echo phpversion() ?></td>
                        </tr>
                        <tr>
                            <td>MySQL Extension</td>
                            <td><span style="color: <?php echo function_exists('mysql_connect') ? 'green;' : 'red;' ?>"><?php echo function_exists('mysql_connect') ? 'Installed' : 'Not installed' ?></span></td>
                        </tr>
                        <tr>
                            <td>OpenSSL Extension</td>
                            <td><span style="color: <?php echo function_exists('openssl_sign') ? 'green;' : 'red;' ?>"><?php echo function_exists('openssl_sign') ? 'Installed' : 'Not installed' ?></span></td>
                        </tr>
                        <tr>
                            <td>GD Extension</td>
                            <td><span style="color: <?php echo function_exists('gd_info') ? 'green;' : 'red;' ?>"><?php echo function_exists('gd_info') ? 'Installed' : 'Not installed' ?></span></td>
                        </tr>
                        <tr>
                            <td>cURL Extension</td>
                            <td><span style="color: <?php echo function_exists('curl_init') ? 'green;' : 'red;' ?>"><?php echo function_exists('curl_init') ? 'Installed' : 'Not installed' ?></span></td>
                        </tr>
                        <tr>
                            <td colspan="2"><strong>Stripe</strong></td>
                        </tr>
                        <tr>
                            <td>Publishable Key</td>
                            <td><span style="<?php echo $this->config->item('stripe_publishable_key') ? '' : 'color: red;' ?>"><?php echo $this->config->item('stripe_publishable_key') ? $this->config->item('stripe_publishable_key') : 'Not set' ?></span></td>
                        </tr>
                        <tr>
                            <td>Secret Key</td>
                            <td><span style="<?php echo $this->config->item('stripe_secret_key') ? '' : 'color: red;' ?>"><?php echo $this->config->item('stripe_secret_key') ? '**********' : 'Not set' ?></span></td>
                        </tr>
                        <tr>
                            <td colspan="2"><strong>Email</strong></td>
                        </tr>
                        <tr>
                            <td>Transport</td>
                            <td><?php $eo = $this->config->item('email_opts'); echo $eo['protocol'] == 'mail' ? 'built-in' : $eo['protocol']; ?></td>
                        </tr>
                        <tr>
                            <td>Default From</td>
                            <td><?php echo $this->config->item('email_from') ? $this->config->item('email_from') : 'No address set - <span style="color:red;">Email disabled</span>' ?></td>
                        </tr>
                        <tr>
                            <td colspan="2"><strong>Upload Storage</strong></td>
                        </tr>
                        <tr>
                            <td>Transport</td>
                            <td><?php echo $this->config->item('s3_bucket') ? 'Amazon S3' : 'Local' ?></td>
                        </tr>
                        <?php if ($this->config->item('s3_bucket')) : ?>
                            <tr>
                                <td>S3 Access key</td>
                                <td>
                                    <?php echo $this->config->item('s3_access_key') ? 'Set' : 'Not set' ?>
                                </td>
                            </tr>
                            <tr>
                                <td>S3 Secret key</td>
                                <td>
                                    <?php echo $this->config->item('s3_secret_key') ? 'Set' : 'Not set' ?>
                                </td>
                            </tr>
                        <?php else : ?>
                            <tr>
                                <td>Local Path</td>
                                <td>
                                    <?php echo rtrim(FCPATH, '/').'/uploads' ?> -
                                    <span style="color:<?php echo is_writable(rtrim(FCPATH, '/').'/uploads') ? ' green' : ' red' ?>;"><?php echo is_writable(rtrim(FCPATH, '/').'/uploads') ? 'Writable' : 'Not writable' ?></span>
                                </td>
                            </tr>
                        <?php endif ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="small-12 medium-3 columns">
            <div class="box side-bar">
                <h3>Need help?</h3>
                <p>Head over to <a href="https://github.com/Minds-On-Design-Lab/giving-impact" target="_blank">GitHub</a> to:</p>
                <ul>
                    <li><a href="https://github.com/Minds-On-Design-Lab/giving-impact/issues" target="_blank">Report a bug</a></li>
                    <li><a href="https://github.com/Minds-On-Design-Lab/giving-impact" target="_blank">Check for new releases</a></li>
                    <li><a href="https://github.com/Minds-On-Design-Lab/Giving-Impact-API" target="_blank">Read the docs</a></li>
                </ul>
            </div>
        </div>
    </div>
</div> <!-- eo main content -->

<?php $this->load->view('components/_footer') ?>
