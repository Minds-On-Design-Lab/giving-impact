<?php $this->load->view('components/_header') ?>
<?php $this->load->view('components/_nav') ?>

<script src="<?php echo base_url('assets/javascripts/d3.v2.js') ?>"></script>
<script src="<?php echo base_url('assets/javascripts/xcharts.min.js') ?>"></script>

<script type="text/javascript">
    var chartCount = 1;
    function drawThermometer(donationTotal, donationTarget, selectString) {
        var data = {
          "xScale": "ordinal",
          "yScale": "linear",
          "yMax": donationTarget/100,
          "yMin": "0",
          "main": [
            {
              "className": ".donations",
              "data": [
                {
                  "x": "Progress",
                  "y": donationTotal/100
                }
              ]
            }
          ]
        };
        var opts = {
            "axisPaddingTop": "10",
            "tickFormatY": function (y) {
                var scale = d3.format(",");
                return "$" + scale(y);
            }
        }
        var myChart = new xChart('bar', data, selectString, opts);
    };
</script>

<?php $this->load->view('dashboard/_nav', array(
    'active' => 'campaigns'
)) ?>

<div id="page">
    <div id="page-header">
        <div class="row">
            <div class="small-12 medium-7 columns">
                <h1 class="page-title">Your latest giving activity</h1>
            </div>
            <div class="small-12 medium-5 columns">
                <a href="<?php echo site_url('campaigns/new') ?>" class="gi-button page-title">Create a New Campaign</a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="small-12 medium-9 columns">
            <?php if (!$campaigns) : ?>
            <div class="box">
            <h2>Looks like you don't have any active campaigns</h2>
            <p>For Giving Impact Pros, you know you simply need to click the <strong>Create a New Campaign</strong> button to create one.</p>
            <p>For new Giving Impact Organizations, you might want to consider the following getting started steps.</p>
            <ol>
                <li>Update Account Settings</li>
                <li>Create/Connect your Stripe Account for Payment Processing</li>
                <li>Create Your First Campaign</li>
                <li>Integrate with Your Website(s)</li>
            </ol>
            <p>In addition, we have some great <a href="http://givingimpact.com/docs" target="_blank">documentation</a> to support and guide you along the way.</p>
            </div>
            <?php endif ?>
            <ul class="small-block-grid-1 medium-block-grid-3">
            <?php $i = 1 ?>
            <?php foreach( $campaigns as $campaign ) : ?>
                <li>
                    <div class="campaign-block">

                        <h2><a href="<?php echo site_url('campaigns/'.$campaign->id_token) ?>"><?php echo $campaign->title ?></a></h2>

                        <div id="chart_<?php echo $i ?>">
                            <figure style="width: 100%; height: 220px;" id="chart_<?php echo $i ?>" class="chart"></figure>

                        </div>
                    </div>
                </li>
            <?php $i++ ?>
            <?php endforeach ?>
            </ul>
        </div>
        <div class="small-12 medium-3 columns" id="right">
           <div class="side-bar">
                <h3>Active Campaigns</h3>
                <ul class="simple-list">
                    <?php foreach( $campaigns as $campaign ) : ?>
                    <li>
                        <h4><a href="<?php echo site_url('campaigns/'.$campaign->id_token) ?>"><?php echo $campaign->title ?></a></h4>
                    </li>
                    <?php endforeach ?>
                </ul>
                <p><a href="<?php echo site_url('dashboard/inactive') ?>">View inactive campaigns</a>
            </div>
        </div>
    </div>
</div> <!-- eo main content -->

<?php foreach( $campaigns as $campaign ) : ?>
    <script type="text/javascript">
        var divID = "#chart_" + chartCount;
        var selectTarget = divID + ' .chart';
        drawThermometer(<?php echo round($campaign->donation_total) ?>, <?php echo round($campaign->donation_target) ?>, selectTarget);
        chartCount++;
    </script>
<?php endforeach ?>

<?php $this->load->view('components/_footer') ?>
