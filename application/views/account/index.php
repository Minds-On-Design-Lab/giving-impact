<?php $this->load->view('components/_header') ?>
<?php $this->load->view('components/_nav') ?>

<div id="page">
    <div id="page-header">
        <div class="row">
            <div class="small-12 columns">
                <h1 class="page-title">Account Settings</h3>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="small-12 medium-9 columns">

            <div class="box">
                <h2>Organization Information
                     <span><a href="<?php echo site_url('account/edit/org') ?>" class="gi-button">Edit Organization Settings</a></span>
                </h2>

                <dl>
                    <dt>Organization Name:</dt>
                    <dd><?php echo $account->account_name ?></dd>

                    <dt>Organizational Address:</dt>
                    <dd>
                        <?php if( $account->street_address ) : ?>
                            <?php echo $account->street_address ?>
                            <?php $account->street_address_2 ? '<br />'.$account->street_address_2 : '' ?>
                            <br />
                            <?php echo $account->city ?>, <?php echo $account->state ?>
                            <br />
                            <?php echo $account->mailing_postal_code ?>
                        <?php endif ?>
                    </dd>

                    <dt>Logo:</dt>
                    <dd><?php if( $account->image_url ) : ?><img src="<?php echo $account->image_url ?>"><?php endif ?></dd>

                </dl>

            </div>

            <div class="box">
                <h2>Account User
                     <span><a href="<?php echo site_url('account/edit/user') ?>" class="gi-button">Edit User Settings</a></span>
                </h2>

                <dl>
                    <dt>First Name:</dt>
                    <dd><?php echo $account->first_name ?></dd>

                    <dt>Last Name:</dt>
                    <dd><?php echo $account->last_name ?></dd>

                    <dt>Username/Email Address:</dt>
                    <dd><?php echo $user->email ?></dd>

                    <dt>Password:</dt>
                    <dd>**********</dd>

                    <dt>Timezone:</dt>
                    <dd><?php echo $account->timezone ?></dd>
                </dl>

            </div>

            <div class="box">
                <h2>Donation Processing &amp; Management</h2>

                <dl>
                    <dt>Country: <span>Country of Stripe account at time of setup.</span></dt>
                    <dd><?php echo $stripe_account->country ?></dd>
                    <dt>Currency: <span>You must select a currency before donations can be processed. Once selected, your currency cannot be changed.</span></dt>
                    <dd>
                        <?php if( empty($currencies) || $account->currency ) : ?>
                            <?php echo strtoupper($account->currency) ?>
                        <?php else : ?>
                            <form method="post" action="<?php echo site_url('account/edit/currency') ?>" id="select-currency">
                                <select name="currency">
                                    <option value="">Select a currency</option>
                                    <?php foreach( $currencies as $c ) : ?>
                                        <option value="<?php echo $c ?>"><?php echo strtoupper($c) ?></option>
                                    <?php endforeach ?>
                                </select>
                            </form>
                        <?php endif ?>
                    </dd>
                </dl>
            </div>

            <div class="box">
                <h2>API Access
                     <span><a href="<?php echo site_url('account/edit/token') ?>" class="gi-button">Regenerate API Key</a></span>
                </h2>

                <p>
                    Use the private key to interact with our developer API and the public key for our donation API.
                </p>

                <div class="row">
                    <div class="eight columns">
                        <dl>
                            <dt>Private API Key:</dt>
                            <dd><input type="text" class="input-text" readonly value="<?php echo $user->single_access_token ?>" /></dd>

                            <dt>Public API Key:</dt>
                            <dd><input type="text" class="input-text" readonly value="<?php echo $user->public_access_token ?>" /></dd>
                        </dl>
                    </div>
                </div>
            </div>

        </div>
        <div class="small-12 medium-3 columns" id="right">
        </div>
    </div>
</div> <!-- eo page -->


<?php $this->load->view('components/_footer') ?>
