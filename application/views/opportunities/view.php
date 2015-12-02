<?php $this->load->view('components/_header') ?>
<?php $this->load->view('components/_nav') ?>

<div class="row">
    <div class="small-12 columns">
        <p class="backlink"><a href="<?php echo site_url('campaigns/'.$campaign->id_token.'/opportunities') ?>">&laquo; Back to Campaign</a></p>
    </div>
</div>

<?php $this->load->view('opportunities/_nav', array(
    'active' => 'overview'
)) ?>

<div id="page">
    <div id="page-header">
        <div class="row">
            <div class="small-12 medium-7 columns">
                <h1 class="page-title"><?php echo $opportunity->title ?></h1>
            </div>
            <div class="small-12 medium-5 columns">
                <span><a href="<?php echo site_url('campaigns/'.$campaign->id_token.'/opportunities/'.$opportunity->id_token.'/edit') ?>" class="gi-button page-title">Edit Opportunity</a></span>
            </div>
        </div>
    </div>
    <div class="row">
      <div class="small-12 medium-9 columns">


            <div class="box">
            <h2>General Information</h2>
            <div class="campaign-desc"><?php echo auto_typography($opportunity->description); ?></div>

            <?php if ($opportunity->campaign_responses) : ?>
              <dl class="campaign-responses">
              <?php foreach( $opportunity->campaign_responses as $response ) : ?>
                <dt><?php echo $response->field_label ?></dt>
                <dd><?php echo $response->response ?></dd>
              <?php endforeach ?>
              </dl>
            <?php endif ?>
            </div>
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
            </div>
        </div>
        <div class="small-12 medium-3 columns">
            <?php $this->load->view('opportunities/_sidebar') ?>
        </div>
    </div>
</div><!-- eo page -->

<?php $this->load->view('components/_footer') ?>
