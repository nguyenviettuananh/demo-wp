<!DOCTYPE html>
<?php $mts_options = get_option(MTS_THEME_NAME); ?>
<html class="no-js" <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame -->
	<!--[if IE ]>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<![endif]-->
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<?php mts_meta(); ?>
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	<?php wp_head(); ?>
</head>
<body id="blog" <?php body_class('main'); ?> itemscope itemtype="http://schema.org/WebPage">       
	<header class="main-header" role="banner" itemscope itemtype="http://schema.org/WPHeader">
		<?php if( $mts_options['mts_sticky_nav'] == '1' ) { ?>
		<div id="catcher"></div>
		<div id="sticky" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
		<div class="inner-sticky">
		<?php } ?>
			<div class="container">
				<div id="header">
					<div class="logo-wrap">
						<?php if ($mts_options['mts_logo'] != '') { ?>
							<?php if( is_front_page() || is_home() || is_404() ) { ?>
									<h1 id="logo" class="image-logo" itemprop="headline">
										<a href="<?php echo home_url(); ?>"><img src="<?php echo $mts_options['mts_logo']; ?>" alt="<?php bloginfo( 'name' ); ?>"></a>
									</h1><!-- END #logo -->
							<?php } else { ?>
								  <h2 id="logo" class="image-logo" itemprop="headline">
										<a href="<?php echo home_url(); ?>"><img src="<?php echo $mts_options['mts_logo']; ?>" alt="<?php bloginfo( 'name' ); ?>"></a>
									</h2><!-- END #logo -->
							<?php } ?>
						<?php } else { ?>
							<?php if( is_front_page() || is_home() || is_404() ) { ?>
									<h1 id="logo" class="text-logo" itemprop="headline">
										<a href="<?php echo home_url(); ?>"><?php bloginfo( 'name' ); ?></a>
									</h1><!-- END #logo -->
							<?php } else { ?>
								  <h2 id="logo" class="text-logo" itemprop="headline">
										<a href="<?php echo home_url(); ?>"><?php bloginfo( 'name' ); ?></a>
									</h2><!-- END #logo -->
							<?php } ?>
						<?php } ?>
					</div>
					<?php if ( $mts_options['mts_show_secondary_nav'] == '1' ) { ?>			
						<div class="secondary-navigation" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
							<a href="#" id="pull" class="toggle-mobile-menu"><?php _e('Menu','mythemeshop'); ?></a>
							<nav id="navigation" class="clearfix mobile-menu-wrapper">
								<?php if ( has_nav_menu( 'secondary-menu' ) ) { ?>
									<?php wp_nav_menu( array( 'theme_location' => 'secondary-menu', 'menu_class' => 'menu clearfix', 'container' => '', 'walker' => new mts_menu_walker ) ); ?>
								<?php } else { ?>
									<ul class="menu clearfix">
										<?php wp_list_categories('title_li='); ?>
									</ul>
								<?php } ?>
							</nav>
						</div>  
					<?php } ?>			
					<?php if(!empty($mts_options['mts_header_search'])) { ?>
						<form class="search-top search-form" action="<?php echo home_url(); ?>" method="get">
							<input class="hideinput" name="s" id="s" type="search" placeholder="<?php _e('Search the Site...'); ?>" autocomplete="off" x-webkit-speech="x-webkit-speech" /><a href="#" class="fa fa-search"></a>
						</form>
					<?php } ?>
		        	<?php mts_cart(); ?>
				</div><!--#header-->
			</div><!--.container-->
		<?php if( $mts_options['mts_sticky_nav'] == '1' ) { ?>
		</div>
		</div>
		<?php } ?>
	</header>