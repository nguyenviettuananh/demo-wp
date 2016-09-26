<?php $mts_options = get_option(MTS_THEME_NAME);
// default = 3
$top_footer_num  = empty($mts_options['mts_top_footer_num']) ? 3 : $mts_options['mts_top_footer_num'];
$bottom_footer_num = empty($mts_options['mts_bottom_footer_num']) ? 3 : $mts_options['mts_bottom_footer_num'];
?>
	</div><!--#page-->
</div><!--.main-container-->
<footer class="footer" role="contentinfo" itemscope itemtype="http://schema.org/WPFooter">
    <?php if ($mts_options['mts_top_footer']) : ?>
        <div class="footerTop">
            <div class="container">
                <div class="footer-widgets top-footer-widgets widgets-num-<?php echo $top_footer_num; ?>">
                <?php
                for ( $i = 1; $i <= $top_footer_num; $i++ ) {
                    $sidebar = ( $i == 1 ) ? 'footer-top' : 'footer-top-'.$i;
                    $class = ( $i == $top_footer_num ) ? 'f-widget last f-widget-'.$i : 'f-widget f-widget-'.$i;
                    ?>
                    <div class="<?php echo $class;?>">
                        <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar( $sidebar ) ) : ?><?php endif; ?>
                    </div>
                    <?php
                }
                ?>
                </div><!--.top-footer-widgets-->
            </div><!--.container-->
        </div>
    <?php endif; ?>

    <?php if ($mts_options['mts_bottom_footer']) : ?>
        <div class="footerBottom">
            <div class="container">            
                <div class="footer-widgets bottom-footer-widgets widgets-num-<?php echo $bottom_footer_num; ?>">
                <?php
                for ( $i = 1; $i <= $bottom_footer_num; $i++ ) {
                    $sidebar = ( $i == 1 ) ? 'footer-bottom' : 'footer-bottom-'.$i;
                    $class = ( $i == $bottom_footer_num ) ? 'f-widget last f-widget-'.$i : 'f-widget f-widget-'.$i;
                    ?>
                    <div class="<?php echo $class;?>">
                        <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar( $sidebar ) ) : ?><?php endif; ?>
                    </div>
                    <?php
                }
                ?>
                </div><!--.bottom-footer-widgets-->
            </div><!--.container-->
        </div>
    <?php endif; ?>

    <div class="copyrights">
        <div class="container">
            <?php mts_copyrights_credit(); ?>
        </div><!--.container-->
    </div> 

</footer><!--footer-->
<?php mts_footer(); ?>
<?php wp_footer(); ?>
</body>
<!--Start of Zopim Live Chat Script-->
<script type="text/javascript">
window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=
d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
_.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute("charset","utf-8");
$.src="//v2.zopim.com/?4EHx5W4Ji7VlpspcJ6e0qiuTppkERMfX";z.t=+new Date;$.
type="text/javascript";e.parentNode.insertBefore($,e)})(document,"script");
</script>
<!--End of Zopim Live Chat Script-->
</html>