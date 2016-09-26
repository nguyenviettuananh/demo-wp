<?php get_header(); ?>
<?php $mts_options = get_option(MTS_THEME_NAME); ?>
	<div class="main-container">
<div id="page" class="<?php mts_single_page_class(); ?> clearfix">
	    <?php if($mts_options['mts_single_carousel'] == '1') { ?>
			<div class="carousel-container loading">
				<div class="single-carousel">
					<ul class="slides">
						<?php $slider_cat = 1; if($mts_options['mts_single_carousel_cat'] != '') { $slider_cat = implode(",", $mts_options['mts_single_carousel_cat']); } $my_query = new WP_Query('cat='.$slider_cat.'&posts_per_page=13');
							while ($my_query->have_posts()) : $my_query->the_post(); ?>
						<li>
							<a href="<?php the_permalink() ?>">
								<div class="slides-shadow"></div>
								<?php if ( has_post_thumbnail() ) { ?> 
									<?php the_post_thumbnail('featured',array('title' => '')); ?>
								<?php } else { ?>
									<div class="featured-thumbnail">
										<img src="<?php echo get_template_directory_uri(); ?>/images/nothumbfeatured.png" class="attachment-featured wp-post-image" alt="<?php the_title(); ?>">
									</div>
								<?php } ?>
								<h6 class="carouseltitle"><span><?php echo mts_truncate(get_the_title(), 32); ?></span></h6>
							    <?php if (function_exists('wp_review_show_total')) wp_review_show_total(true, 'latestPost-review-wrapper'); ?>
		                  	</a>
	                    </li>
						<?php endwhile; wp_reset_query(); ?>
					</ul>
				</div>
			</div>
			<!-- carousel-container -->
		<?php } ?>
	<?php get_template_part('templates/single-header-animation' ); ?>
	<article class="<?php mts_article_class(); ?>">
		<div id="content_box" >
			<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
				<div id="post-<?php the_ID(); ?>" <?php post_class('g post'); ?>>
					<?php
					// Single post parts ordering
					if ( isset( $mts_options['mts_single_post_layout'] ) && is_array( $mts_options['mts_single_post_layout'] ) && array_key_exists( 'enabled', $mts_options['mts_single_post_layout'] ) ) {
						$single_post_parts = $mts_options['mts_single_post_layout']['enabled'];
					} else {
						$single_post_parts = array( 'content' => 'content', 'author' => 'author', 'related' => 'related' );
					}
					foreach( $single_post_parts as $part => $label ) { get_template_part( 'templates/single-post-'.$part ); }
					?>

				</div><!--.g post-->
				<?php comments_template( '', true ); ?>
			<?php endwhile; /* end loop */ ?>
		</div>
	</article>
	<?php get_sidebar(); ?>
<?php get_footer(); ?>