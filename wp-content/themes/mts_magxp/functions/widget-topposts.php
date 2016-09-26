<?php
/*-----------------------------------------------------------------------------------

	Plugin Name: MyThemeShop Top Posts
	Version: 2.0
	
-----------------------------------------------------------------------------------*/


class mts_top_posts_widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
	 		'mts_top_posts_widget',
			__('MyThemeShop: Top Posts','mythemeshop'),
			array( 'description' => __( 'Displays list of most popular posts within a time range.','mythemeshop' ) )
		);
	}

 	public function form( $instance ) {
		$defaults = array(
            'title' => __( 'Top Posts','mythemeshop' ),
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
		echo self::get_top_posts( $posts, $days, $show_comment_num );
		echo $after_widget;
	}

	public function get_top_posts( $posts, $days, $show_comment_num ) {
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
            
		echo '<ul class="top-posts">';
        $i = 0;
		foreach($popular as $post) :
			setup_postdata($post);
			?>
		<li>
            <div class="number"><?php echo ++$i; ?></div>
      		<div class="post-title">
    			<a href="<?php the_permalink(); ?>">
    				<?php echo mts_truncate(get_the_title(), 96); ?>	
    			</a>
    		</div>
    		<div class="postinfo">
    			<?php if ( $show_comment_num == 1 ) : ?>
                    <div class="comment_num">
    				    <?php echo comments_number(__('No Comment','mythemeshop'), __('One Comment','mythemeshop'), '<span class="comm">%</span> '.__('Comments','mythemeshop'));?>
                    </div>
                <?php endif; ?>
            </div>
		</li>	
		<?php endforeach; wp_reset_postdata();
		echo '</ul>'."\r\n";
	}

}
add_action( 'widgets_init', create_function( '', 'register_widget( "mts_top_posts_widget" );' ) );