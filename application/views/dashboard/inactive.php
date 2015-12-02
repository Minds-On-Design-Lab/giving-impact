<?php $this->load->view('components/_header') ?>
<?php $this->load->view('components/_nav') ?>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js"></script>

<div class="row">
    <div class="small-12 columns">
        <p class="backlink"><a href="<?php echo site_url('dashboard') ?>">&laquo; Back to Active Campaigns</a></p>
    </div>
</div>

<?php $this->load->view('dashboard/_nav', array(
    'active' => 'campaigns'
)) ?>

<div id="page">
    <div id="page-header">
        <div class="row">
            <div class="small-12 medium-7 columns">
                <h1 class="page-title">Inactive Campaigns</h1>
            </div>
            <div class="small-12 medium-5 columns">
                <a href="<?php echo site_url('campaigns/new') ?>" class="gi-button page-title">Create a New Campaign</a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="small-12 medium-9 columns">

            <div class="box">
                <h2>
                    Inactive Campaigns
                </h2>


                <table role="grid">
                    <thead>
                        <tr>
                            <th>Title</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach( $inactive as $c ) : ?>
                        <tr>
                            <td><a href="<?php echo site_url('campaigns/'.$c->id_token) ?>"><?php echo $c->title ?></a></td>
                    <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="small-12 medium-3 columns" id="right">

        </div>
    </div>
</div> <!-- eo main content -->

<?php $this->load->view('components/_footer') ?>
