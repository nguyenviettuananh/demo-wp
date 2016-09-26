<?php
$mts_options = get_option(MTS_THEME_NAME);
$header_animation = mts_get_post_header_effect();
?>
<?php if ( 'parallax' === $header_animation ) {?>
	<?php if (mts_get_thumbnail_url()) : ?>
        <div id="parallax" <?php echo 'style="background-image: url('.mts_get_thumbnail_url().');"'; ?>></div>
    <?php endif; ?>
<?php } else if ( 'zoomout' === $header_animation ) {?>
	 <?php if (mts_get_thumbnail_url()) : ?>
        <div id="zoom-out-effect"><div id="zoom-out-bg" <?php echo 'style="background-image: url('.mts_get_thumbnail_url().');"'; ?>></div></div>
    <?php endif; ?>
<?php } ?>