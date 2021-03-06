<?php
/**
 * Add support for WooCommerce.
 *
 * @see https://wordpress.org/plugins/woocommerce/
 * @package granule
 */

/**
 * Add support for woocommerce
 */
function granule_wc_support() {

	add_theme_support( 'woocommerce' );

	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

}

add_action( 'after_setup_theme', 'granule_wc_support' );


/**
 * Remove Jetpack related posts from WooCommerce products
 *
 * @param  array $options Related posts options.
 * @return array
 */
function granule_wc_remove_related_posts( $options ) {

	if ( ! class_exists( 'WooCommerce' ) ) {
		return $options;
	}

	if ( is_product() ) {
		$options['enabled'] = false;
	}

	return $options;

}

add_filter( 'jetpack_relatedposts_filter_options', 'granule_wc_remove_related_posts' );


/**
 * Check to see if the current page is a WooCommerce page or not.
 *
 * @return boolean
 */
function granule_is_woocommerce() {

	if ( ! class_exists( 'WooCommerce' ) ) {
		return false;
	}

	if ( is_woocommerce() ) {
		return true;
	}

	if ( is_account_page() ) {
		return true;
	}

	if ( is_checkout() || is_cart() ) {
		return true;
	}

	return false;

}


/**
 * Disable sidebar on WooCommerce pages
 *
 * @param  boolean $is_active_sidebar Current value of sidebar visibility.
 * @param  boolean $index             Sidebar to test.
 * @return boolean                    Whether to display the sidebar or not.
 */
function granule_wc_is_sidebar_active( $is_active_sidebar, $index ) {

	if ( ! class_exists( 'WooCommerce' ) ) {
		return $is_active_sidebar;
	}

	if ( 'sidebar-1' === $index ) {

		// Not WooCommerce so return default.
		if ( ! granule_is_woocommerce() ) {
			return $is_active_sidebar;
		}

		return false;

	}

	return $is_active_sidebar;

}

add_filter( 'is_active_sidebar', 'granule_wc_is_sidebar_active', 10, 2 );


/**
 * Remove default WooCommerce actions
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );


/**
 * Remove support for Jetpack infinite scroll for WooCommerce products since it
 * uses a different html structure.
 *
 * @param  WP_Query $query The current archive object.
 */
function granule_cpt_archives_settings( $query ) {

	// Define the context.
	// Not on dashboard pages when inside the main query only on cpt archives.
	if ( ! is_admin() && $query->is_main_query() && is_post_type_archive( 'product' ) ) {

		// Remove infinite scroll inside this context.
		remove_theme_support( 'infinite-scroll' );

	}

}

add_action( 'pre_get_posts', 'granule_cpt_archives_settings' );
