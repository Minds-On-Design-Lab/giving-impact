<?php $this->load->view('components/_header') ?>
<?php $this->load->view('components/_nav') ?>


<?php $this->load->view('campaigns/_nav', array(
    'active' => 'overview'
)) ?>

<div id="page">
    <div id="page-header">
        <div class="row">
            <div class="small-12 medium-7 columns">
                <h1 class="page-title"><?php echo $campaign->title ?></h1>
            </div>
            <div class="small-12 medium-5 columns">
                <a href="<?php echo site_url('campaigns/'.$campaign->id_token.'/edit') ?>" class="gi-button page-title"> Edit Campaign</a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="small-12 medium-9 columns">
            <div class="box">
                <h2>Recent Activity</h4>
                <table role="grid">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Donations</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach( $stats as $stat ) : ?>
                            <tr>
                                <td class="width30"><?php echo format_date($stat->date, 'm/d/Y') ?></td>
                                <td class="width20"><?php echo $stat->total_donations ?></td>
                                <td class="width20"><?php echo money_format('%n', $stat->donation_total/100) ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
                <div id="pagination">
                    <?php if( $previous ) : ?><a href="<?php echo site_url($previous) ?>" class="left">&#8249; Previous</a><?php else : ?>&nbsp;<?php endif ?>
                    <?php if( $next ) : ?><a href="<?php echo site_url($next) ?>" class="right">Next &#8250;</a><?php else : ?>&nbsp;<?php endif ?>
                </div>
            </div>
        </div>
        <div class="small-12 medium-3 columns">
            <?php $this->load->view('campaigns/_sidebar', array('campaign' => $campaign)) ?>
        </div>
    </div>
</div><!-- eo page -->

<?php $this->load->view('components/_footer') ?>
