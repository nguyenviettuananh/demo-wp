<?php
$options = get_option(MTS_THEME_NAME);
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
get_header('shop'); ?>
<div class="main-container">
	<div id="page" class="clearfix">
		<article class="<?php mts_article_class(); ?>">
			<div id="content_box" >
					<div class="single_post">
				<?php do_action('woocommerce_before_main_content'); ?>
					<?php while ( have_posts() ) : the_post(); ?>
						<?php woocommerce_get_template_part( 'content', 'single-product' ); ?>
					<?php endwhile; // end of the loop. ?>
				<?php do_action('woocommerce_after_main_content'); ?>
					</div>
					<?php woocommerce_output_related_products(); ?>
			</div>
		</article>
		<?php /*do_action('woocommerce_sidebar');*/ ?>
		<?php get_sidebar(); ?>
	<?php get_footer(); ?>