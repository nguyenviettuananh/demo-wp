<?php $mts_options = get_option(MTS_THEME_NAME); ?>
<?php get_header(); ?>
<?php if ( !is_paged() ) { ?>
	<?php if ( is_home() && $mts_options['mts_featured_slider'] == '1' ) { ?>
		<div class="banner clearfix">
			<section class="slider">
				<div id="slider" class="flexslider clearfix">
					<ul class="slides">
						<?php $first_image = '';
						$last_image = '';
						$slider_cat = implode(",", $mts_options['mts_featured_slider_cat']); $my_query = new WP_Query('cat='.$slider_cat.'&posts_per_page=7');
						while ($my_query->have_posts()) : $my_query->the_post(); 
							if ($first_image == '') $first_image = get_the_post_thumbnail(get_the_ID(), 'slider', array('title' => '')); ?>
							<li> 
								<div class="slides-shadow"></div>
								<?php the_post_thumbnail('slider',array('title' => '')); ?>
								<div class="banner-content">
									<h2 class="slidertitle"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>
									<?php mts_the_postinfo( 'single' ); ?>
								</div>
							</li>
						<?php endwhile; $last_image = get_the_post_thumbnail(get_the_ID(), 'slider', array('title' => '')); $my_query->rewind_posts(); ?>
					</ul>
				</div>
				<div class="slider-extra">
					<div class="first"><div class="slides-shadow"></div><?php echo $first_image; ?></div>
					<div class="last"><div class="slides-shadow"></div><?php echo $last_image; ?></div>
				</div>
				<div id="carousel" class="flexslider">
					<ul class="slides thumbs">
						<?php while ($my_query->have_posts()) : $my_query->the_post();
						$image_id = get_post_thumbnail_id();
						$image_url = wp_get_attachment_image_src($image_id, 'sliderthumb');
						$image_url = $image_url[0]; ?>
						<li>
							<img src="<?php echo $image_url; ?>" />
							<div class="arrowTop"></div>
						</li>
						<?php endwhile; wp_reset_query(); ?>
					</ul>
				</div>
			</section>
		</div><!-- slider-container -->
	<?php } ?>
<?php } ?>
<div class="main-container">
	<div id="page" class="clearfix">
		<div class="article">
			<div id="content_box">
				<?php if (is_home() && !is_paged()) { ?>
	                <?php if($mts_options['mts_gallery_slider'] == '1' && $mts_options['mts_gallery_position'] == '0') { ?>
	                    <!-- Gallery Slider -->
	                    <div class="photo-gallery top clearfix">
	                        <section class="slider inner-slider">
	                            <div id="slider1" class="flexslider">
	                                <ul class="slides">
	                                <?php $slider_cat = implode(",", $mts_options['mts_gallery_slider_cat']); $my_query = new WP_Query('cat='.$slider_cat.'&posts_per_page=5');
	                                      while ($my_query->have_posts()) : $my_query->the_post(); ?>
	                                    <li>
	                                        <div class="slides-shadow"></div>
	                                        <?php the_post_thumbnail('featured-blog',array('title' => '')); ?>
	                                        <div class="banner-content">
	                                            <h2 class="slidertitle"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>
	                                        </div>
	                                    </li>
	                                    <?php endwhile; $my_query->rewind_posts(); ?>
	                                </ul>
	                            </div>
	                            <div id="carouse2" class="flexslider">
	                                <ul class="slides">
	                                <?php while ($my_query->have_posts()) : $my_query->the_post();
	                                        $image_id = get_post_thumbnail_id();
	                                        $image_url = wp_get_attachment_image_src($image_id, 'sliderthumb');
	                                        $image_url = $image_url[0]; ?>
	                                    <li>
	                                        <img src="<?php echo $image_url; ?>" />
	                                        <div class="arrowTop"></div>
	                                    </li>
	                                <?php endwhile; wp_reset_query(); ?>
	                                </ul>
	                            </div>
	                        </section>
	                    </div>
	                    <!-- End Gallery Slider -->
	                <?php } ?>
	            <?php } ?>

				<?php if ( !is_paged() ) {
					$featured_categories = array();
					if ( !empty( $mts_options['mts_featured_categories'] ) ) {
						foreach ( $mts_options['mts_featured_categories'] as $section ) {
							$category_id = $section['mts_featured_category'];
							$featured_categories[] = $category_id;
							$posts_num = $section['mts_featured_category_postsnum'];
							if ( 'latest' == $category_id ) { ?>
								<div id="content_box_inner"> 
									<h3 class="featured-category-title"><?php _e('Latest','mythemeshop'); ?></h3>
	                                <div class="clear">
										<?php $j = 0; if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
											<article class="latestPost excerpt <?php if(!empty($mts_options['mts_homepage_layout'])) { echo "layout-2"; } else { echo "layout-1"; } echo (++$j % 3 == 0) ? ' last' : ''; ?>">
												<?php mts_archive_post(); ?>
											</article>
										<?php endwhile; endif; ?>
	                                </div>
	                            </div>
								
								<?php if ( $j !== 0 ) { // No pagination if there is no posts ?>
									<?php get_template_part( 'templates/archive-pagination' ); ?>
								<?php } ?>
								
							<?php } else { // if $category_id != 'latest': ?>
								<div id="content_box_inner"> 
									<h3 class="featured-category-title"><a href="<?php echo esc_url( get_category_link( $category_id ) ); ?>" title="<?php echo esc_attr( get_cat_name( $category_id ) ); ?>"><?php echo get_cat_name( $category_id ); ?></a></h3>
	                                <div class="clear">
										<?php $j = 0; $cat_query = new WP_Query('cat='.$category_id.'&posts_per_page='.$posts_num);
										if ( $cat_query->have_posts() ) : while ( $cat_query->have_posts() ) : $cat_query->the_post(); ?>
											<article class="latestPost excerpt <?php if(!empty($mts_options['mts_homepage_layout'])) { echo "layout-2"; } else { echo "layout-1"; } echo (++$j % 3 == 0) ? ' last' : ''; ?>">
												<?php mts_archive_post(); ?>
											</article>
										<?php
										endwhile; endif; wp_reset_query(); ?>
	                                </div>
	                            </div>

							<?php }
						}
					} ?>

				<?php } else { //Paged
					$j = 0; if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
						<article class="latestPost excerpt <?php if(!empty($mts_options['mts_homepage_layout'])) { echo "layout-2"; } else { echo "layout-1"; } echo (++$j % 3 == 0) ? ' last' : ''; ?>">
							<?php mts_archive_post(); ?>
						</article>
					<?php endwhile; endif;
					if ( $j !== 0 ) { // No pagination if there is no posts
						get_template_part( 'templates/archive-pagination' );
					}
				} ?>

				<?php if (is_home() && !is_paged()) { ?>
	                <?php if($mts_options['mts_gallery_slider'] == '1' && $mts_options['mts_gallery_position'] == '1') { ?>
	                    <!-- Gallery Slider -->
	                    <div class="photo-gallery clearfix">
	                        <h3><?php _e('Gallery', 'mythemeshop'); ?></h3>
	                        <section class="slider inner-slider">
	                            <div id="slider1" class="flexslider">
	                                <ul class="slides">
	                                <?php $slider_cat = implode(",", $mts_options['mts_gallery_slider_cat']); $my_query = new WP_Query('cat='.$slider_cat.'&posts_per_page=5');
	                                      while ($my_query->have_posts()) : $my_query->the_post(); ?>
	                                    <li>
	                                        <div class="slides-shadow"></div>
	                                        <?php the_post_thumbnail('featured-blog',array('title' => '')); ?>
	                                        <div class="banner-content">
	                                            <h2 class="slidertitle"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h2>
	                                        </div>
	                                    </li>
	                                    <?php endwhile; $my_query->rewind_posts(); ?>
	                                </ul>
	                            </div>
	                            <div id="carouse2" class="flexslider">
	                                <ul class="slides">
		                                <?php while ($my_query->have_posts()) : $my_query->the_post();
	                                        $image_id = get_post_thumbnail_id();
	                                        $image_url = wp_get_attachment_image_src($image_id, 'sliderthumb');
	                                        $image_url = $image_url[0]; ?>
		                                    <li>
		                                        <img src="<?php echo $image_url; ?>" />
		                                        <div class="arrowTop"></div>
		                                    </li>
		                                <?php endwhile; wp_reset_query(); ?>
	                                </ul>
	                            </div>
	                        </section>
	                    </div>
	                    <!-- End Gallery Slider -->
	                <?php } ?>
	            <?php } ?>
			</div>
		</div>
		<?php get_sidebar(); ?>
	<?php get_footer(); ?>