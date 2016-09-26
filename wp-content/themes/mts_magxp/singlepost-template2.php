<?php get_header(); ?>
<?php $mts_options = get_option(MTS_THEME_NAME); ?>
	<div class="main-container">
	<?php if ( have_posts() ) while ( have_posts() ) : the_post(); 
	get_template_part('templates/single-header-animation-template2' ); ?>
	<div id="page" class="<?php mts_single_page_class(); ?> clearfix">
		<article class="<?php mts_article_class(); ?>">
			<div id="content_box" >
				<?php //if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
					<div id="post-<?php the_ID(); ?>" <?php post_class('g post'); ?>>
						<?php
						// Single post parts ordering
						if ( isset( $mts_options['mts_single_post_layout'] ) && is_array( $mts_options['mts_single_post_layout'] ) && array_key_exists( 'enabled', $mts_options['mts_single_post_layout'] ) ) {
							$single_post_parts = $mts_options['mts_single_post_layout']['enabled'];
						} else {
							$single_post_parts = array( 'content2' => 'content', 'author' => 'author', 'related' => 'related' );
						}
						foreach( $single_post_parts as $part => $label ) { 
							
							if($part == 'content'){
								$part = 'content2';
							}

							get_template_part( 'templates/single-post-'.$part ); 
						}
						?>

					</div><!--.g post-->
					<?php comments_template( '', true ); ?>
				<?php endwhile; /* end loop */ ?>
			</div>
		</article>
		<?php get_sidebar(); ?>
	<?php get_footer(); ?>