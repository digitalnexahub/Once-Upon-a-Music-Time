<?php
/**
 * Plugin Name: WASHTOWER Design
 * Description: Site design, WooCommerce styling and custom Elementor widgets (Product Carousel, Review Slider) for the WASHTOWER store.
 * Version: 1.0.0
 * Requires Plugins: woocommerce, elementor
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/* Installed layout: wp-content/plugins/washtower-design/ with the two files below in includes/
   (source of truth in this repo: washtower-redesign/washtower-design.php and wt-elementor-widgets.php). */
if ( ! function_exists( 'wt_icon' ) ) {
	require_once __DIR__ . '/includes/washtower-design.php';
}
if ( ! defined( 'WT_ASSET_VER' ) ) {
	require_once __DIR__ . '/includes/wt-elementor-widgets.php';
}
