<?php
$options = get_option(MTS_THEME_NAME);
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
get_header('shop'); ?>

<?php if (is_shop() && !is_paged()) { ?>
	<?php if($mts_options['mts_shop_slider'] == '1') { ?>
    <div class="banner clearfix">
		<section class="slider">
			<div id="slider" class="flexslider">
				<ul class="slides">
					<?php 
                    $first_image = '';
                    $last_image = '';
                    foreach($mts_options['mts_custom_shop_slider'] as $slide) : 
                        // remove size information
                        $image_id = $slide['mts_custom_shop_slider_image'];
                        if ($first_image == '') $first_image = wp_get_attachment_image($image_id, 'slider', false, array('title' => ''));
                        ?>
					<li> 
                        <div class="slides-shadow"></div>
                        <?php echo wp_get_attachment_image($image_id, 'slider', false, array('title' => '')); ?>
                        <div class="banner-content">
                            <h2 class="slidertitle">
                                <a href="<?php echo $slide['mts_custom_shop_slider_link']; ?>">
                                    <?php echo $slide['mts_custom_shop_slider_title']; ?>
                                </a>
                            </h2>
                            <div class="post-info"><?php echo $slide['mts_custom_shop_slider_text']; ?></div>
                        </div>
					</li>
					<?php endforeach; $last_image = wp_get_attachment_image($image_id, 'slider', false, array('title' => '')); ?>
				</ul>
			</div>
            <div class="slider-extra">
                <div class="first"><div class="slides-shadow"></div><?php echo $first_image; ?></div>
                <div class="last"><div class="slides-shadow"></div><?php echo $last_image; ?></div>
            </div>
		</section>
    </div><!-- slider-container -->
    <?php } ?>
<?php } ?>

<?php if (is_shop() && !is_paged()) { ?>
	<!--Features-->
	<?php if($mts_options['mts_shop_features'] == '1') { ?>
    <div id="page">
		<section class="features">
			<?php 
            	foreach($mts_options['mts_custom_shop_features'] as $slide) : 
                // remove size information
                ?>
                <div class="feature">
                <?php if($slide['mts_custom_shop_feature_link'] != ''){ ?>
					<a href="<?php echo $slide['mts_custom_shop_feature_link']; ?>">
				<?php } ?>
						<div class="left">
	                       <i class="fa fa-<?php echo $slide['mts_custom_shop_feature_icon']; ?>"></i>
						</div>
                        <div class="feature-content">
                            <p class="featuretitle">
                                <?php echo $slide['mts_custom_shop_feature_title']; ?>
                            </p>
                            <div class="feature-info"><?php echo $slide['mts_custom_shop_feature_text']; ?></div>
                        </div>
                <?php if($slide['mts_custom_shop_feature_link'] != ''){ ?>
                    </a>
                <?php } ?>
                </div>
			<?php endforeach; ?>
		</section>
    </div><!-- slider-container -->
    <?php } ?>
<?php } ?>
<div class="main-container">
	<div id="page" class="clearfix">
		<article class="<?php mts_article_class(); ?>">
			<div id="content_box" >
				<?php do_action('woocommerce_before_main_content'); ?>
					<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
						<h1 class="page-title"><?php woocommerce_page_title(); ?></h1>
					<?php endif; ?>
					<?php do_action( 'woocommerce_archive_description' ); ?>
					<?php if ( have_posts() ) : ?>
						<?php do_action( 'woocommerce_before_shop_loop' ); ?>
						<?php woocommerce_product_loop_start(); ?>
							<?php woocommerce_product_subcategories(); ?>
							<?php while ( have_posts() ) : the_post(); ?>
								<?php woocommerce_get_template_part( 'content', 'product' ); ?>
							<?php endwhile; // end of the loop. ?>
						<?php woocommerce_product_loop_end(); ?>
						<?php do_action( 'woocommerce_after_shop_loop' ); ?>
					<?php elseif ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) : ?>
						<?php woocommerce_get_template( 'loop/no-products-found.php' ); ?>
					<?php endif; ?>
				<?php do_action('woocommerce_after_main_content'); ?>
			</div>
		</article>
		<?php /*do_action('woocommerce_sidebar');*/ ?>
		<?php get_sidebar(); ?>
	<?php get_footer(); ?>