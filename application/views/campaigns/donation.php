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
        <div class="small-12 medium-9 columns">
            <div class="box">
                <h2>
                    <?php $donation->offline ? 'Offline ' : '' ?>Donation
                    <span class="right"><a href="<?php echo site_url('campaigns/'.$campaign->id_token.'/donations/'.$donation->id_token.'/edit') ?>" class="gi-button">Edit Donation</a></span>
                </h2>

                <dl>
                    <dt>Name:</dt>
                    <dd><?php echo $donation->first_name ?> <?php echo $donation->last_name ?></dd>

                    <dt>Donation Date:</dt>
                    <dd><?php echo date('m/d/Y', strtotime($donation->donation_date)) ?></dd>

                    <dt>Amount:</dt>
                    <dd><?php echo money_format('%n', $donation->donation_total/100) ?></dd>

                    <?php if( $campaign->donation_levels ) : ?>
                        <dt>Donation Level:</dt>
                        <dd><?php echo $donation->donation_level ? $donation->donation_level : '<em>No level selected</em>' ?></dd>
                    <?php endif ?>

                    <dt>Email:</dt>
                    <dd>
                        <?php if( $donation->email_address ) : ?>
                            <a href="mailto:<?php echo $donation->email_address ?>"><?php echo $donation->email_address ?></a><br />
                            We<?php echo $donation->contact ? ' have ' : ' <strong>do not</strong> have ' ?>permission to follow-up
                        <?php else : ?>
                            <em>No email provided</em>
                        <?php endif ?>
                    </dd>

                    <dt>Address:</dt>
                    <dd>
                        <?php if( $donation->billing_address1 ) : ?>
                            <?php echo $donation->billing_address1 ?><br />
                            <?php echo $donation->billing_city ?>, <?php echo $donation->billing_state ?><br />
                            <?php echo $donation->billing_postal_code ?> <?php echo $donation->country ?>
                        <?php else : ?>
                            <em>No or incomplete address</em>
                        <?php endif ?>
                    </dd>

                    <?php if ($donation->plan && $donation->plan->id) : ?>
                        <dt>Recurring Donation</dt>
                        <dd><a href="<?php echo $donation->plan->stripe_url ?>" target="_blank">View plan on Stripe</a></dd>
                    <?php endif ?>

                    <?php if( $campaign->custom_fields ) : ?>
                        <?php foreach( $donation->custom_responses as $field ) : ?>
                            <?php if( !$field->status ) : continue; endif ; ?>
                            <dt><?php echo $field->field_label ?>:</dt>
                            <dd><?php echo $field->response ? $field->response : '<em>No response</em>' ?></dd>
                        <?php endforeach ?>
                    <?php endif ?>
                </dl>
            </div> <!-- eo box -->
            <div class="box">
              <h2>Stripe Transaction Log</h2>

              <table role="grid">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>ID</th>
                    <th>Amount</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <?php $txns = $this->Transaction_model->for_donation($donation) ?>
                  <?php if( count($txns) ) : ?>
                    <?php foreach( $txns as $txn ) : ?>
                      <tr>
                        <td><?php echo date('m/d/Y H:m', strtotime($txn->created_at)) ?> UTC</td>
                        <td><?php echo $txn->type ?></td>
                        <td><?php echo $txn->stripe_id ?></td>
                        <td><?php echo money_format('%n', $txn->total/100) ?></td>
                        <td><?php if( $txn->type == 'charge' ) : ?><?php if( $donation->refunded ) : ?>Refunded<?php else: ?><a href="#" data-reveal-id="refund-modal" class="gi-button small">Refund</a><?php endif ?><?php endif ?></td>
                        </tr>
                      <?php endforeach ?>
                    <?php else : ?>
                      <tr>
                        <td colspan="5">
                          <em>Sorry, transaction log not available for this donation</em>
                        </td>
                      </tr>
                    <?php endif ?>
                  </tbody>
                </table>


            </div>
        </div>
        <div class="small-12 medium-3 columns">
            <?php $this->load->view('supporters/_supporter_sidebar.php') ?>
        </div>
    </div>
</div> <!-- eo page -->

<div id="refund-modal" class="reveal-modal" data-reveal>
  <div class="box">
    <h2>Refund</h2>
    <p>Are you sure you want to refund this donation for <?php echo $donation->donation_total/100 ?>? Please note: you cannot undo this action.</p>

    <div class="text-left">
        <form method="post" action="<?php echo site_url('campaigns/'.$campaign->id_token.'/donations/'.$donation->id_token.'/refund') ?>">
            <input type="hidden" name="_rf" value="<?php echo $donation->id_token ?>" />
            <input type="submit" value="Issue Refund" class="button" /> |
            <a href="#" class="close-reveal-modal">Cancel</a>
        </form>
    </div>
  </div>
</div>

<?php $this->load->view('components/_footer') ?>
