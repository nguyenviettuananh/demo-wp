<?php $mts_options = get_option(MTS_THEME_NAME); ?>
<div class="featured-wrap clearfix">
	<a href="<?php the_permalink() ?>" title="<?php the_title(); ?>" rel="nofollow" class="post-image post-image-left"<?php if (mts_get_category_color()) { echo ' style="border-color: '.mts_get_category_color().';"'; } ?>>
		<?php echo '<div class="featured-thumbnail">'; the_post_thumbnail('featured',array('title' => '')); echo '</div>'; ?>
		<?php if (function_exists('wp_review_show_total')) wp_review_show_total(true, 'latestPost-review-wrapper'); ?>
	</a>
	<?php $category = get_the_category();  if(!empty($category)){ ?>
	<div class="inner-categories"<?php if (mts_get_category_color()) { echo ' style="background: '.mts_get_category_color().';"'; } ?>><?php echo '<a href="'.get_category_link( $category[0]->term_id ).'" title="' . esc_attr( sprintf( __( "View all posts in %s", 'mythemeshop' ), $category[0]->name ) ) . '">'.$category[0]->cat_name.'</a>'; ?></div>
	<?php } ?>
</div>
<header>
	<h2 class="title front-view-title">
		<a href="<?php the_permalink() ?>" title="<?php the_title(); ?>"><?php 
			$text = get_the_title();
			$text_content = wp_trim_words( $text, 6, ' ...' );
			echo $text_content; ?>
		</a>
	</h2>
	<?php mts_the_postinfo('home'); ?>
</header>