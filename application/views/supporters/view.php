<?php $this->load->view('components/_header') ?>
<?php $this->load->view('components/_nav') ?>

<?php $this->load->view('dashboard/_nav', array(
    'active' => 'supporters'
)) ?>

<div id="page">

    <div id="page-header">
        <div class="row">
            <div class="small-12 medium-7 columns">
                <h1 class="page-title"><?php echo $supporter->first_name ?> <?php echo $supporter->last_name ?></h1>
            </div>
            <div class="small-12 medium-5 columns">
                 <a href="<?php echo site_url('supporters/'.$supporter->id_token.'/edit/') ?>" class="gi-button page-title">Edit Supporter</a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="small-12 medium-9 columns">
            <div class="box">
                <h2>Donation Activity</h2>
                <table role="grid">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Campaign</th>
                            <th>Opportunity</th>
                            <th>Donation</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach( $donations as $donation ) : ?>
                            <?php
                                if( $donation->campaign ) {
                                    $donation_url = 'campaigns/'.$donation->campaign->id_token;
                                } else {
                                    $donation_url = 'campaigns/'.$donation->opportunity->campaign->id_token.'/opportunities/'.$donation->opportunity->id_token;
                                }
                            ?>
                            <tr>
                                <td class="width15"><a href="<?php echo site_url($donation_url.'/donations/'.$donation->id_token) ?>"><?php echo format_date($donation->donation_date, 'm/d/Y') ?></a></td>
                                <td class="width35"><?php echo (isset($donation->campaign->title)) ? $donation->campaign->title : $donation->opportunity->campaign->title ?></td>
                                <td class="width35"><?php echo (isset($donation->opportunity->title)) ? $donation->opportunity->title : '' ?></td>
                                <td class="width15"><?php echo money_format('%n', $donation->donation_total/100) ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
                <div id="pagination">
                    <?php if( $previous ) : ?><a href="<?php echo site_url($previous) ?>" class="left">&#8249; Previous</a><?php else : ?>&nbsp;<?php endif ?>
                    <?php if( $next ) : ?><a href="<?php echo site_url($next) ?>" class="right">Next &#8250;</a><?php else : ?>&nbsp;<?php endif ?>
                </div>
            </div> <!-- eo box -->

            <?php if( $opportunities && count($opportunities) ) : ?>
                <div class="box">
                    <h2>Opportunity Leadership</h2>
                    <table class="twelve">
                        <thead>
                            <tr>
                                <th>Campaign</th>
                                <th>Opportunity</th>
                                <th>Donation Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach( $opportunities as $opp ) : ?>
                                <?php
                                    $opp_url = 'campaigns/'.$opp->campaign->id_token.'/opportunities/'.$opp->id_token;
                                ?>
                                <tr>
                                  <td class="width40"><a href="<?php echo site_url($opp_url) ?>"><?php echo $opp->campaign->title ?></a></td>
                                    <td class="width40"><a href="<?php echo site_url($opp_url) ?>"><?php echo $opp->title ?></a></td>
                                    <td class="width20"><?php echo money_format('%n', $opp->donation_total/100) ?></td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div> <!-- eo box -->
            <?php endif ?>
        </div> <!-- of nine columns -->
        <div class="small-12 medium-3 columns">
          <div class="box">
            <h2>Contact Information</h2>
            <p>
              <?php echo $supporter->first_name ?> <?php echo $supporter->last_name ?><br />
              <?php echo $supporter->email_address ?>
            </p>
            <p>
              <?php echo $supporter->street_address ? $supporter->street_address.'<br />' : '' ?>
              <?php echo $supporter->city ? $supporter->city.', ' : '' ?> <?php echo $supporter->state ? $supporter->state : '' ?> <?php echo $supporter->postal_code ? $supporter->postal_code : '' ?>
              <?php if( $supporter->city || $supporter->state || $supporter->postal_code ) : ?><br /><?php endif ?>
                <?php echo $supporter->country ? $supporter->country : '' ?>
            </p>
            <h2>Overall Metrics</h2>
            <ul class="side-bar">
              <li>Total: <span class="value"><?php echo money_format('%n', $supporter->donations_total/100) ?></span></li>
              <li>Donations: <span class="value"><?php echo $supporter->total_donations ?></span></li>
            </ul>
          </div>
        </div>
    </div>
</div> <!-- eo page -->

<?php $this->load->view('components/_footer') ?>
