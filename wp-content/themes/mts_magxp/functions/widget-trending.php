<?php
/*-----------------------------------------------------------------------------------

	Plugin Name: MyThemeShop Trending Posts
	Version: 2.0
	
-----------------------------------------------------------------------------------*/


class mts_trending_posts_widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
	 		'mts_trending_posts_widget',
			__('MyThemeShop: Trending Posts','mythemeshop'),
			array( 'description' => __( 'Displays list of most popular posts in a visual way.','mythemeshop' ) )
		);
	}

 	public function form( $instance ) {
		$defaults = array(
            'title' => __( 'Trending Posts','mythemeshop' ),
            'posts' => 5,
			'days' => 30,
			'show_comment_num' => 1
		);
		$instance = wp_parse_args((array) $instance, $defaults);
        extract($instance);
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:','mythemeshop' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'posts' ); ?>"><?php _e( 'Number of Posts to show','mythemeshop' ); ?></label> 
			<input id="<?php echo $this->get_field_id( 'posts' ); ?>" name="<?php echo $this->get_field_name( 'posts' ); ?>" type="number" min="1" step="1" value="<?php echo $posts; ?>" />
		</p>
        
		<p>
	       <label for="<?php echo $this->get_field_id( 'days' ); ?>"><?php _e( 'Popular limit (days):', 'mythemeshop' ); ?>
	       <input id="<?php echo $this->get_field_id( 'days' ); ?>" name="<?php echo $this->get_field_name( 'days' ); ?>" type="number" min="1" step="1" value="<?php echo $days; ?>" />
	       </label>
       </p>
		
		<p>
			<label for="<?php echo $this->get_field_id("show_comment_num"); ?>">
				<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("show_comment_num"); ?>" name="<?php echo $this->get_field_name("show_comment_num"); ?>" value="1" <?php if (isset($instance['show_comment_num'])) { checked( 1, $instance['show_comment_num'], true ); } ?> />
				<?php _e( 'Show Number of Comments', 'mythemeshop'); ?>
			</label>
		</p>
		
		<?php 
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['posts'] = intval( $new_instance['posts'] );
		$instance['days'] = intval( $new_instance['days'] );
		$instance['show_comment_num'] = intval( $new_instance['show_comment_num'] );
		return $instance;
	}

	public function widget( $args, $instance ) {
		extract( $args );
        extract( $instance );
		echo $before_widget;
		if ( ! empty( $title ) ) echo $before_title . $title . $after_title;
		echo self::get_trending_posts( $posts, $days, $show_comment_num );
		echo $after_widget;
	}

	public function get_trending_posts( $posts, $days, $show_comment_num ) {
		global $post;
        $popular_days = array();
		if ( $days ) {
			$popular_days = array(
        		//set date ranges
        		'after' => "$days day ago",
        		'before' => 'today',
        		//allow exact matches to be returned
        		'inclusive'         => true,
        	);
		}
		
		$popular = get_posts( array( 
            'suppress_filters' => false, 
            'ignore_sticky_posts' => 1, 
            'orderby' => 'comment_count', 
            'numberposts' => $posts,
            'date_query' => $popular_days) );
            
		echo '<ul class="trending-posts">';
        // calculate min and max
        // min will be 80%, max 100%
        $comment_numbers = array();
        foreach($popular as $post) {
            $comment_count = wp_count_comments( $post->ID );
            $comment_numbers[] = $comment_count->total_comments;
        }
        $last = count($comment_numbers) - 1;
        $max = $comment_numbers[0];
        $min = $comment_numbers[$last];
        $difference = $max - $min;
        if ($difference > 0) {
            $one_unit = 5 / $difference;
        } else {
            $one_unit = 0;
        }
        
        $bar_widths = array();
        foreach ($comment_numbers as $num) {
            $bar_widths[] = round(100 - (($max - $num) * $one_unit), 2);
        }
        
        $i = 0;
		foreach($popular as $post) :
            
			setup_postdata($post);
			?>
		<li>
			<a href="<?php the_permalink(); ?>" class="bar" style="width: <?php echo $bar_widths[$i]; ?>%;">
                <div class="bar-style">
    				<h4><?php echo mts_truncate(get_the_title(), 96); ?></h4>	
                    <div class="vertical-text">
                    <?php if ( $show_comment_num == 1 ) : ?>
    				    <span><?php echo comments_number('0', '1', '%');?></span>
                    <?php endif; ?>
                    </div>
                </div>
            </a>
		</li>	
		<?php $i++; endforeach; wp_reset_postdata();
		echo '</ul>'."\r\n";
	}

}
add_action( 'widgets_init', create_function( '', 'register_widget( "mts_trending_posts_widget" );' ) );