<?php
/**
 * Plugin Name: Variation Price Display Range for WooCommerce
 * Plugin URI: https://wpxtension.com/product/variation-price-display/
 * Description: Adds lots of advanced options to control how you display the price for your WooCommerce variable products.
 * Author: WPXtension
 * Version: 1.4.1
 * Domain Path: /languages
 * Requires at least: 5.8
 * Tested up to: 6.9
 * Requires PHP: 7.2
 * WC requires at least: 5.5
 * WC tested up to: 10.4.3
 * Text Domain: variation-price-display
 * Author URI: https://wpxtension.com/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

defined( 'ABSPATH' ) or die( 'Keep Silent' );

if ( ! defined( 'VARIATION_PRICE_DISPLAY_PLUGIN_FILE' ) ) {
    define( 'VARIATION_PRICE_DISPLAY_PLUGIN_FILE', __FILE__ );
}

if( ! defined( 'VARIATION_PRICE_DISPLAY_REQUIRED_PRO_VERSION' ) ){
    define( 'VARIATION_PRICE_DISPLAY_REQUIRED_PRO_VERSION', '1.4.0' );
}

if ( ! defined( 'VARIATION_PRICE_DISPLAY_MAYBE_PRO_PLUGIN_FILE' ) ) {
    $variation_price_display_maybe_pro_plugin_file = sprintf('%s/variation-price-display-pro/variation-price-display-pro.php', wp_normalize_path( WP_PLUGIN_DIR ));
    define( 'VARIATION_PRICE_DISPLAY_MAYBE_PRO_PLUGIN_FILE', $variation_price_display_maybe_pro_plugin_file );
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


// Plugin check
function variation_price_display_version_check_companion(){
    return defined( 'VARIATION_PRICE_DISPLAY_PRO_PLUGIN_VERSION' ) && ( version_compare( VARIATION_PRICE_DISPLAY_PRO_PLUGIN_VERSION, VARIATION_PRICE_DISPLAY_REQUIRED_PRO_VERSION ) >= 0 );
}

function variation_price_display_deactivate_companion(){
    if ( variation_price_display_version_check_companion() ) {
        return;
    }

    if ( ! function_exists( 'is_plugin_active' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    if ( is_plugin_active( 'variation-price-display-pro/variation-price-display-pro.php' ) ) {
        
        unset($_GET['activate']);

        add_action( 'admin_notices', 'variation_price_display_companion_error_msg' );
        
        // Deactivate the plugin silently, Prevent deactivation hooks from running.
        deactivate_plugins( 'variation-price-display-pro/variation-price-display-pro.php', true );
    }
}
add_action( 'plugins_loaded',  'variation_price_display_deactivate_companion');

function variation_price_display_companion_error_msg(){
    /* translators: %1$s: Main wrapper start, %2$s: Main wrapper end, %3$s: Bold wrapper start, %4$s: Bold wrapper end, %5$s: Pro Plugin Version */
    printf(esc_html__('%1$sYou are running an older version of %3$s"Variation Price Display Range for WooCommerce - Pro"%4$s. Please upgrade to %3$s %5$s %4$s or higher.%2$s', 'variation-price-display'), 
        '<div class="error notice"><p>',
        '</p></div>',
        '<b>',
        '</b>',
        esc_html(constant( 'VARIATION_PRICE_DISPLAY_REQUIRED_PRO_VERSION' )) 
    );
}

// Meta notice
add_action( 'after_plugin_row_meta', 'variation_price_display_companion_meta_notice', 10, 2 );
function variation_price_display_companion_meta_notice( string $plugin_file, array $plugin_data) {
    if ( plugin_basename( VARIATION_PRICE_DISPLAY_MAYBE_PRO_PLUGIN_FILE ) === $plugin_file ) {
        $current_version = $plugin_data['Version'];
        if (  version_compare( $current_version, constant( 'VARIATION_PRICE_DISPLAY_REQUIRED_PRO_VERSION' ), '<' )  ) {
            /* translators: %s: Pro Plugin Version */
            $notice_text =   sprintf(esc_html__('You are running an older version of "Variation Price Display Range for WooCommerce - Pro". Please upgrade to %s or higher.', 'variation-price-display'), esc_html(constant( 'VARIATION_PRICE_DISPLAY_REQUIRED_PRO_VERSION' )));

            printf( '<p style="color: darkred"><span class="dashicons dashicons-warning"></span> <strong>%s</strong></p>', esc_html($notice_text) );
        }
    }
}