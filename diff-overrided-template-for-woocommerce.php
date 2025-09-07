<?php

/**
 * Plugin Name:     Diff Overrided Template Files for WooCommerce
 * Plugin URI:      https://github.com/mariovalney/diff-overrided-template-for-woocommerce
 * Description:     A tool to help developers fix template files.
 * Version:         1.0.0
 * License:         GPLv2 or later
 * Author:          Mário Valney
 * Author URI:      https://mariovalney.com/me
 * Text Domain:     diff-overrided-template-for-woocommerce
 * Requires PHP:    8.2
 *
 * WC requires at least: 3.6.5
 * WC tested up to: 10.1.0
 *
 */

// If this file is called directly, call the cops.
defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

define( 'DOT_PLUGIN_URL', plugins_url( '', __FILE__ ) );

if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
    require_once WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __FILE__ ) ) . '/includes/class-debug-theme.php';
}
