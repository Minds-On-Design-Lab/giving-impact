<?php $this->load->view('components/_header') ?>
<?php $this->load->view('components/_nav') ?>

<?php $this->load->view('campaigns/_nav', array(
    'active' => 'donation'
)) ?>

<div id="page">

    <div id="page-header">
        <div class="row">
            <div class="small-12 columns">
                <h1 class="page-title"><?php echo $campaign->title ?></h1>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="small-12 columns">

            <div class="box">
                <h2>
                    Donations
                    <span class="right"><a href="<?php echo site_url('campaigns/'.$campaign->id_token.'/donations/new') ?>" class="gi-button">Add Offline Entry</a>
                    <a href="<?php echo site_url('campaigns/'.$campaign->id_token.'/donations.csv') ?>" class="gi-button">Export Log (CSV)</a>
                    </span>
                </h2>


                <table role="grid">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Opportunity</th>
                            <th>Recurring</th>
                            <th>Total</th>
                            <th>Offline</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach( $donations as $donation ) : ?>
                            <tr>
                                <td class="width20"><a href="<?php echo site_url('campaigns/'.$campaign->id_token.'/donations/'.$donation->id_token) ?>"><?php echo format_date($donation->donation_date, 'm/d/Y') ?></a></td>
                                <td class="width20"><?php echo $donation->first_name.' '.$donation->last_name ?></td>
                                <td class="width20"><?php echo ($donation->opportunity) ? $donation->opportunity->title : '' ?></td>
                                <td class="width10"><?php if ($donation->plan && $donation->plan->id) : ?><i class="fa fa-check action"></i><?php endif ?></td>
                                <td class="width10"><?php if( $donation->refunded ) : ?><strike><?php endif ?><?php echo money_format('%n', $donation->donation_total/100) ?><?php if( $donation->refunded ) : ?></strike><?php endif ?></td>
                                <td class="width10"><?php if( $donation->offline ) : ?><i class="fa fa-check fa-lg"></i><?php endif ?></td>
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
    </div>
</div> <!-- eo page -->

<?php $this->load->view('components/_footer') ?>
