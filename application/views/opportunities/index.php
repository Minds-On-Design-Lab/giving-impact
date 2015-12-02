<?php $this->load->view('components/_header') ?>
<?php $this->load->view('components/_nav') ?>

<?php $this->load->view('campaigns/_nav', array(
    'active' => 'opportunity'
)) ?>

<div id="page">

    <div id="page-header">
        <div class="row">
            <div class="twelve columns">
                <h1 class="page-title"><?php echo $campaign->title ?></h1>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="small-12 medium-9 columns">

            <div class="box">

                    <h2>Active Giving Opportunities
                    <span><a href="<?php echo site_url('campaigns/'.$campaign->id_token.'/opportunities/new') ?>" class="gi-button">Add a Giving Opportunity</a>
                    <a href="<?php echo site_url('campaigns/'.$campaign->id_token.'/opportunities/opportunities.csv') ?>" class="gi-button">Export Log (CSV)</a></span>
                    </h2>

                <table role="grid">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Total</th>
                            <th>Target</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach( $opportunities as $opportunity ) : ?>
                            <tr>
                                <td><a href="<?php echo site_url('campaigns/'.$campaign->id_token.'/opportunities/'.$opportunity->id_token) ?>"><?php echo $opportunity->title ?></a></td>
                                <td><?php echo money_format('%n', $opportunity->donation_total/100) ?></td>
                                <td><?php echo money_format('%n', $opportunity->donation_target/100) ?></td>
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
        <div class="small-12 medium-3 columns" id="right">
            <div class="side-bar">
                <h3>Opportunities</h3>
                <ul class="simple-list">
                    <li>
                        <h4><a href="<?php echo site_url('campaigns/'.$campaign->id_token.'/opportunities/inactive') ?>">View inactive opportunities</a></h4>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div> <!--eo page-->

<?php $this->load->view('components/_footer') ?>
