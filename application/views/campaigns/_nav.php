<?php if( $campaign->id_token ) : ?>
<div id="page-tabs">
	<div class="row">
	    <div class="small-12 columns">
					<dl class="tabs opportunity-nav">
          	<dd<?php if( $active == 'overview' ) : ?> class="active"<?php endif ?>><a href="<?php echo site_url('campaigns/'.$campaign->id_token) ?>">Campaign Overview</a></dd>
            <dd<?php if( $active == 'donation' ) : ?> class="active"<?php endif ?>><a href="<?php echo site_url('campaigns/'.$campaign->id_token.'/donations') ?>">Donations</a></dd>
            <!--<dd<?php if( $active == 'setup' ) : ?> class="active"<?php endif ?>><a href="#">Publishing Setup</a></dd>-->
            <?php if( $campaign->has_giving_opportunities ) : ?><dd<?php if( $active == 'opportunity' ) : ?> class="active"<?php endif ?>><a href="<?php echo site_url('campaigns/'.$campaign->id_token.'/opportunities') ?>">Giving Opportunities</a></dd><?php endif ?>
	        </dl>
	    </div>
	</div>
</div>
<?php endif ?>
