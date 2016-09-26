<?php $mts_options = get_option(MTS_THEME_NAME); ?>
<?php if (isset($mts_options['mts_pagenavigation_type']) && $mts_options['mts_pagenavigation_type'] == '1' ) { ?>
	<?php mts_pagination(); ?> 
<?php } else { ?>
	<div class="pagination pagination-previous-next">
		<ul>
			<li class="nav-previous"><?php next_posts_link( '&larr; '. __( 'Older Posts', 'mythemeshop' ) ); ?></li>
			<li class="nav-next"><?php previous_posts_link( __( 'Newer Posts', 'mythemeshop' ).' &rarr;' ); ?></li>
		</ul>
	</div>
<?php } ?>