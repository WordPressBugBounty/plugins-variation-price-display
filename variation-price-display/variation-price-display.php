<?php
/**
 * Plugin Name: Variation Price Display Range for WooCommerce
 * Plugin URI: https://wordpress.org/plugins/variation-price-display
 * Description: Adds lots of advanced options to control how you display the price for your WooCommerce variable products.
 * Author: WPXtension
 * Version: 1.3.15
 * Domain Path: /languages
 * Requires at least: 5.8
 * Tested up to: 6.6
 * Requires PHP: 7.2
 * WC requires at least: 5.5
 * WC tested up to: 9.2
 * Text Domain: variation-price-display
 * Author URI: https://wpxtension.com/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

defined( 'ABSPATH' ) or die( 'Keep Silent' );

if ( ! defined( 'VARIATION_PRICE_DISPLAY_PLUGIN_FILE' ) ) {
    define( 'VARIATION_PRICE_DISPLAY_PLUGIN_FILE', __FILE__ );
}


// Include the main class.
if ( ! class_exists( 'Variation_Price_Display', false ) ) {
    require_once dirname( __FILE__ ) . '/includes/class-variation-price-display.php';
}

// Require woocommerce admin message
function variation_price_display_wc_requirement_notice() {

    if ( ! class_exists( 'WooCommerce' ) ) {

        printf( '<div class="%1$s"><p>%2$s <a class="thickbox open-plugin-details-modal" href="%3$s"><strong>%4$s</strong></a></p></div>', 
            'notice notice-error', 
            wp_kses( __( "<strong>Variation Price Display Range for WooCommerce</strong> is an add-on of ", 'variation-price-display' ), array( 'strong' => array() ) ), 
            esc_url( add_query_arg( array(
                'tab'       => 'plugin-information',
                'plugin'    => 'woocommerce',
                'TB_iframe' => 'true',
                'width'     => '640',
                'height'    => '500',
            ), admin_url( 'plugin-install.php' ) ) ), 
            esc_html__( 'WooCommerce', 'variation-price-display' ) 
        );
    }
}

add_action( 'admin_notices', 'variation_price_display_wc_requirement_notice' );


/**
 * Returns the main instance.
 */

function variation_price_display() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid

    if ( ! class_exists( 'WooCommerce', false ) ) {
        return false;
    }

    if ( function_exists( 'variation_price_display_pro' ) ) {
        return variation_price_display_pro();
    }

    return Variation_Price_Display::instance();
}

add_action( 'plugins_loaded', 'variation_price_display' );


// HPOS compatibility for Variation Price Display Range
function variation_price_display_hpos_compatibility() {
    if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
    }
}

add_action( 'before_woocommerce_init', 'variation_price_display_hpos_compatibility' );