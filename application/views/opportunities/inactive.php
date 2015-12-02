<?php $this->load->view('components/_header') ?>
<?php $this->load->view('components/_nav') ?>


<?php $this->load->view('campaigns/_nav', array(
    'active' => 'opportunity'
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
        <div class="small-12 medium-9 columns">

            <div class="box">

                    <h2>Inactive Giving Opportunities</h2>

                <table role="grid">
                    <thead>
                        <tr>
                            <th>Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!$opportunities) { ?>
                            <tr>
                                <td class="center"><em>There are no inactive Giving Opportunities at this time</em></p></td>
                            </tr>
                        <?php } ?>
                        <?php foreach( $opportunities as $opportunity ) : ?>
                            <tr>
                                <td><a href="<?php echo site_url('campaigns/'.$campaign->id_token.'/opportunities/'.$opportunity->id_token) ?>"><?php echo $opportunity->title ?></a></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="small-12 medium-3 columns" id="right">
            <div class="side-bar">
                <h3>Opportunities</h3>
                <ul class="simple-list">
                    <li>
                        <h4><a href="<?php echo site_url('campaigns/'.$campaign->id_token.'/opportunities') ?>">View active opportunities</a></h4>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div> <!--eo page-->

<?php $this->load->view('components/_footer') ?>
