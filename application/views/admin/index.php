<?php $this->load->view('components/_header') ?>
<?php $this->load->view('components/_nav') ?>

<div id="page">
    <div id="page-header">
        <div class="row">
            <div class="small-12 columns">
                <h1 class="page-title">Admin &amp; Reporting</h1>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="small-12 columns">
            <div class="box">
                <h3>Hooks</h3>

                <table role="grid">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>URI</th>
                            <th>Account</th>
                            <th>Status</th>
                            <th>Last Run</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach( $hooks as $hook ) : ?>
                            <tr>
                                <td><a href="<?php echo site_url('admin/hook/'.$hook->id)?>"><?php echo $hook->event ?></a></td>
                                <td><?php echo $hook->url ?></td>
                                <td><?php echo $hook->account_name ?></td>
                                <td><?php echo $hook->status ? 'Active' : 'Inactive' ?></td>
                                <td><?php echo $hook->last_run ? date('m/d/Y g:ia', $hook->last_run) : 'Never' ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div> <!-- eo main content -->

<?php $this->load->view('components/_footer') ?>
