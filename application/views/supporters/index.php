<?php $this->load->view('components/_header') ?>
<?php $this->load->view('components/_nav') ?>

<?php $this->load->view('dashboard/_nav', array(
    'active' => 'supporters'
)) ?>

<div id="page">
    <div id="page-header">
        <div class="row">
            <div class="small-12 columns">
                <h1 class="page-title">Your Supporters</h1>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="small-12 medium-9 columns">

            <div class="box">
                <h2>All Supporters</h4>
                <table role="grid">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Total Donations</th>
                            <th>Donations Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach( $supporters as $supporter ) : ?>
                            <tr>
                                <td class="width30"><a href="<?php echo site_url('supporters/'.$supporter->id_token) ?>"><?php echo $supporter->last_name ?>, <?php echo $supporter->first_name ?></a></td>
                                <td class="width30"><?php echo $supporter->email_address ?></td>
                                <td class="width20"><?php echo $supporter->total_donations ?></td>
                                <td class="width20"><?php echo money_format('%n', $supporter->donations_total/100) ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
                <div id="pagination">
                    <?php if( $previous ) : ?><a href="<?php echo site_url($previous) ?>" class="left">&#8249; Previous</a><?php else : ?>&nbsp;<?php endif ?>
                    <?php if( $next ) : ?><a href="<?php echo site_url($next) ?>" class="right">Next &#8250;</a><?php else : ?>&nbsp;<?php endif ?>
                </div>
            </div><!-- eo box -->
        </div>
        <div class="small-12 medium-3 columns">

            <div class="box side-bar">
                <h3>Top 5 Most Generous</h3>
                <ol>
                    <?php foreach( $topdonors as $topdonor ) : ?>
                    <li><a href="<?php echo site_url('supporters/'.$topdonor->id_token) ?>"><?php echo $topdonor->first_name ?> <?php echo $topdonor->last_name ?></a> : <?php echo money_format('%n', $topdonor->donations_total/100) ?></li>
                    <?php endforeach ?>
                </ol>
            </div>
            <div class="box side-bar">
                <h3>Top 5 Most Frequent</h3>
                <ol>
                    <?php foreach( $freqdonors as $freqdonor ) : ?>
                    <li><a href="<?php echo site_url('supporters/'.$freqdonor->id_token) ?>"><?php echo $freqdonor->first_name ?> <?php echo $freqdonor->last_name ?></a> : <?php echo $freqdonor->total_donations ?></a></li>
                    <?php endforeach ?>
                </ol>
            </div>

        </div>
    </div>
</div><!-- eo page -->

<?php $this->load->view('components/_footer') ?>
