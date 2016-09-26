<?php $mts_options = get_option(MTS_THEME_NAME); ?>
<div class="single_post">
	<?php if ($mts_options['mts_breadcrumb'] == '1') { ?>
		<div class="breadcrumb" xmlns:v="http://rdf.data-vocabulary.org/#"><?php mts_the_breadcrumb(); ?></div>
	<?php } ?>
	<header>
		<h1 class="title single-title entry-title"><?php the_title(); ?></h1>
		<?php mts_the_postinfo( 'single' ); ?>
	</header><!--.headline_area-->
	<div class="post-single-content box mark-links entry-content">
		<?php if ($mts_options['mts_posttop_adcode'] != '') { ?>
			<?php $toptime = $mts_options['mts_posttop_adcode_time']; if (strcmp( date("Y-m-d", strtotime( "-$toptime day")), get_the_time("Y-m-d") ) >= 0) { ?>
				<div class="topad">
					<?php echo do_shortcode($mts_options['mts_posttop_adcode']); ?>
				</div>
			<?php } ?>
		<?php } ?>
		<?php if (isset($mts_options['mts_social_button_position']) && $mts_options['mts_social_button_position'] !== 'bottom') mts_social_buttons(); ?>
		<div class="thecontent">
			<?php the_content(); ?>
		</div>
		<?php wp_link_pages(array('before' => '<div class="pagination">', 'after' => '</div>', 'link_before'  => '<span class="current"><span class="currenttext">', 'link_after' => '</span></span>', 'next_or_number' => 'next_and_number', 'nextpagelink' => __('Next','mythemeshop'), 'previouspagelink' => __('Previous','mythemeshop'), 'pagelink' => '%','echo' => 1 )); ?>
		<?php if ($mts_options['mts_postend_adcode'] != '') { ?>
			<?php $endtime = $mts_options['mts_postend_adcode_time']; if (strcmp( date("Y-m-d", strtotime( "-$endtime day")), get_the_time("Y-m-d") ) >= 0) { ?>
				<div class="bottomad">
					<?php echo do_shortcode($mts_options['mts_postend_adcode']); ?>
				</div>
			<?php } ?>
		<?php } ?> 
		<?php if (empty($mts_options['mts_social_button_position']) || $mts_options['mts_social_button_position'] == 'bottom') mts_social_buttons(); ?>
	</div><!--.post-single-content-->
</div><!--.single_post-->

<?php if (!empty($mts_options['mts_prevnext_post_links'])) { ?>
	<div class="pagination pagination-single">
		<ul>
			<li><?php next_post_link('%link', '<i class="fa fa-angle-left"></i> %title'); ?></li>
			<li><?php previous_post_link('%link', '<i class="fa fa-angle-right"></i> %title'); ?></li>
		</ul>
	</div>
<?php } ?>