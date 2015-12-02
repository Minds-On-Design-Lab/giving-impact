<?php if( $opportunity->id_token ) : ?>
<div id="page-tabs">
	<div class="row">
	    <div class="small-12 columns">
	        <dl class="tabs opportunity-nav">
	            <dd<?php if( $active == 'overview' ) : ?> class="active"<?php endif ?>><a href="<?php echo site_url('campaigns/'.$campaign->id_token.'/opportunities/'.$opportunity->id_token) ?>">Opportunity Overview</a></dd>
	            <dd<?php if( $active == 'donation' ) : ?> class="active"<?php endif ?>><a href="<?php echo site_url('campaigns/'.$campaign->id_token.'/opportunities/'.$opportunity->id_token.'/donations') ?>">Donations</a></dd>
							<dd<?php if( $active == 'supporters' ) : ?> class="active"<?php endif ?>><a href="<?php echo site_url('campaigns/'.$campaign->id_token.'/opportunities/'.$opportunity->id_token.'/supporters') ?>">Supporters</a></dd>
	        </dl>
	    </div>
	</div>
</div>
<?php endif ?>
