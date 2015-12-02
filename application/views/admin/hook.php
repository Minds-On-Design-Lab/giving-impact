<?php $this->load->view('components/_header') ?>

<?php $this->load->view('components/_nav') ?>

<div id="page">
    <div id="page-header">
        <div class="row">
            <div class="twelve columns">
                <h1 class="page-title">Admin &amp; Reporting</h1>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="small-12 medium-9 columns">
            <div class="box">
                <h2>Hook
                     <span><a href="<?php echo site_url('admin/update_hook/'.$hook->id) ?>" class="gi-button"><?php echo $hook->status ? 'Disable' : 'Enable' ?> Hook</a></span>
                </h2>

                <dl>
                    <dt>Event</dt>
                    <dd><?php echo $hook->event ?></dd>

                    <dt>URI</dt>
                    <dd><?php echo $hook->url ?></dd>

                    <dt>Status</dt>
                    <dd><?php echo $hook->status ? 'Active' : 'Inactive' ?></dd>

                    <dt>Last Run</dt>
                    <dd><?php echo $hook->last_run ? date('m/d/Y g:ia', $hook->last_run) : 'Never' ?></dd>
                </dl>
            </div>
            <div class="box">
                <h2>Pending Calls
                    <span><a href="<?php echo site_url('admin/clear_hook/'.$hook->id) ?>" class="gi-button">Clear All</a></span>
                </h2>
                <table class="twelve">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>First Run</th>
                            <th>Next Run</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if( count($tasks) ) : ?>
                            <?php foreach( $tasks as $task ) : ?>
                                <tr>
                                    <td><?php echo $task->item ?> (<?php echo $task->item_id?>)</td>
                                    <td><?php echo $task->first_run ? date('m/d/Y g:ia', $task->first_run) : 'Never' ?></td>
                                    <td><?php echo $task->next_run ? date('m/d/Y g:ia', $task->next_run) : 'Never' ?></td>
                                </tr>
                            <?php endforeach ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="3">No pending tasks</td>
                            </tr>
                        <?php endif ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div> <!-- eo main content -->

<?php $this->load->view('components/_footer') ?>
