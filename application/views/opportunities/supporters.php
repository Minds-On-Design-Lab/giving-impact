<?php $this->load->view('components/_header') ?>
<?php $this->load->view('components/_nav') ?>

<?php $this->load->view('opportunities/_nav', array(
  'active' => 'supporters'
)) ?>

<div id="page">

  <div id="page-header">
    <div class="row">
      <div class="twelve columns">
        <h1 class="page-title"><?php echo $opportunity->title ?></h3>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="small-12 medium-9 columns">
        <div class="box">
          <h2>
            Lead Supporters
          </h2>
          <p>These supporters are uniquely associated with this Giving Opportunity as its champion(s).</p>
          <table role="grid" class="twelve">
            <thead>
              <tr>
                <th>Name (Last, First)</th>
                <th>Email</th>
                <th>Action(s)</th>
              </tr>
            </thead>
            <tbody>
              <?php if( $opportunity->id_token ) : foreach( $opportunity->supporters as $supporter ) : ?>
                <tr>
                  <td><a href="<?php echo site_url('supporters/'.$supporter->id_token) ?>"><?php echo $supporter->last_name ?>, <?php echo $supporter->first_name ?></a></td>
                  <td><?php echo $supporter->email_address ?></td>
                  <td><a href="<?php echo site_url('campaigns/'.$campaign->id_token.'/opportunities/'.$opportunity->id_token.'/supporters/'.$supporter->id_token.'/remove') ?>" class="remove" data-action="remove-opp-supporter"><i class="fa fa-times-circle fa-lg action"></i></a></td>
                </tr>
              <?php endforeach; endif ?>
            </tbody>
          </table>
      </div>
    </div>
    <div class="small-12 medium-3 columns" id="right">
      <div class="box">
        <h2>Add Supporter</h2>
        <p>Enter an email of a supporter to add to this Giving Opportuntity.</p>

        <form class="side" method="post" action="<?php echo site_url($this->uri->uri_string()) ?>">
          <input type="text" name="supporter_email_address" placeholder="Email Address" />
          <input type="submit" class="gi-button" value="Add Supporter" />
        </form>
      </div>
    </div>
  </div>
</div> <!-- eo page -->

<?php $this->load->view('components/_footer') ?>
