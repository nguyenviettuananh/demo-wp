<?php
/*
Name: WooCommerce Product Image Flipper
URI: http://jameskoster.co.uk/tag/product-image-flipper/
Version: 0.1
Author URI: http://jameskoster.co.uk

	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {


	/**
	 * New Badge class
	 **/
	if ( ! class_exists( 'WC_pif' ) ) {

		class WC_pif {

			public function __construct() {
				add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'woocommerce_template_loop_second_product_thumbnail' ), 11 );
				add_filter( 'post_class', array( $this, 'product_has_gallery' ) );
			}


	        /*-----------------------------------------------------------------------------------*/
			/* Class Functions */
			/*-----------------------------------------------------------------------------------*/

			// Add pif-has-gallery class to products that have a gallery
			function product_has_gallery( $classes ) {
				global $product;

				$post_type = get_post_type( get_the_ID() );

				if ( $post_type == 'product' && ! is_admin() ) {

					$attachment_ids = $product->get_gallery_attachment_ids();

					if ( $attachment_ids ) {
						$classes[] = 'pif-has-gallery';
					}
				}

				return $classes;
			}


			/*-----------------------------------------------------------------------------------*/
			/* Frontend Functions */
			/*-----------------------------------------------------------------------------------*/

			// Display the second thumbnails
			function woocommerce_template_loop_second_product_thumbnail() {
				global $product, $woocommerce;

				$attachment_ids = $product->get_gallery_attachment_ids();

				if ( $attachment_ids ) {
					$secondary_image_id = $attachment_ids['0'];
					echo wp_get_attachment_image( $secondary_image_id, 'shop_catalog', '', $attr = array( 'class' => 'secondary-image attachment-shop-catalog' ) );
				}
			}

		}


		$WC_pif = new WC_pif();
	}
}