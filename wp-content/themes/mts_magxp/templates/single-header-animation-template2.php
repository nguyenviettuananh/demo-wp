<?php
$mts_options = get_option(MTS_THEME_NAME);
$header_animation = mts_get_post_header_effect();
?>
<?php if ( 'parallax' === $header_animation ) {?>
	<?php if (mts_get_thumbnail_url()) : ?>
		<div class="single_post-img"> 
			<div id="parallax" <?php echo 'style="background-image: url('.mts_get_thumbnail_url().');"'; ?>>
				<div class="slides-shadow"></div>
				<header class="clearfix">
					<h1 class="title single-title"><?php the_title(); ?></h1>
					<?php mts_the_postinfo( 'single' ); ?>
				</header>
			</div>
		</div>
    <?php endif; ?>
<?php } else if ( 'zoomout' === $header_animation ) {?>
	 <?php if (mts_get_thumbnail_url()) : ?>
	 	<div class="single_post-img"> 
	 		<div id="zoom-out-effect">
	 			<div id="zoom-out-bg" <?php echo 'style="background-image: url('.mts_get_thumbnail_url().');"'; ?>>
	 				<div class="slides-shadow"></div>
	 				<header class="clearfix">
	 					<h1 class="title single-title"><?php the_title(); ?></h1>
	 					<?php mts_the_postinfo( 'single' ); ?>
	 				</header>
	 			</div>
	 		</div>
	 	</div>
    <?php endif; ?>
<?php } else{?>
	<div class="single_post-img"> 
		<div class="slides-shadow"></div>
		<?php the_post_thumbnail('slider'); ?>
		<header class="clearfix">
			<h1 class="title single-title"><?php the_title(); ?></h1>
			<?php mts_the_postinfo( 'single' ); ?>
		</header>
	</div>
<?php } ?>