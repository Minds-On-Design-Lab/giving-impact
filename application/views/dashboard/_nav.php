<div id="page-tabs">
	<div class="row">
	    <div class="small-12 columns">
	        <dl class="tabs campaign-nav">
	            <dd<?php if( $active == 'campaigns' ) : ?> class="active"<?php endif ?>><a href="<?php echo site_url('dashboard') ?>">Campaigns</a></dd>
	            <dd<?php if( $active == 'supporters' ) : ?> class="active"<?php endif ?>><a href="<?php echo site_url('supporters') ?>">Supporters</a></dd>
	        </dl>
	    </div>
	</div>
</div>
