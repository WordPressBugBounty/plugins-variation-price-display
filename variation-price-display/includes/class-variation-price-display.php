<?php 

defined( 'ABSPATH' ) or die( 'Keep Quit' );

class Variation_Price_Display{

    /*
     * Version of Plugin.
     *
     */

    protected $_plugin = 'variation-price-display';

    protected $_version = '1.3.15';

    protected static $_instance = null;


    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }


    /*
     * Construct of the Class.
     *
     */

    public function __construct(){

        $this->includes();
        $this->init();
        $this->get_wpx_menu();
        $this->get_wpx_setting_fields();

    }


    /*
     * Version function.
     *
     */
    public function version() {
        return esc_attr( $this->_version );
    }

    /*
     * Name function.
     *
     */
    public function plugin() {
        return esc_attr( $this->_plugin.'-pro' );
    }

    /*
     * Bootstraps the class and hooks required actions & filters.
     *
     */
    public function init() {

        // Load TextDomain
        add_action( 'init', array( $this, 'language' ) );

        $this->get_backend();
        $this->get_frontend();
    } 


    /**
     *
     * Load Text Domain Folder
     *
     */
    public function language() {
        load_plugin_textdomain( "variation-price-display", false, basename( dirname( VARIATION_PRICE_DISPLAY_PLUGIN_FILE ) )."/languages" );
    }

    /*
     * Includes files.
     *
     */

    public function includes() {
        require_once dirname( __FILE__ ) . '/wpxtension/wpx-menu.php';
        require_once dirname( __FILE__ ) . '/wpxtension/wpx-setting-fields.php';
        require_once dirname( __FILE__ ) . '/class-variation-price-display-admin-settings.php';
        require_once dirname( __FILE__ ) . '/class-variation-price-display-front.php';
    }

    /**
     *
     * WPX Menu
     *
     */

    public function get_wpx_menu(){
        return WPXtension_Menu::instance();
    }


    /**
     *
     * WPX Setting Fields
     *
     */

    public function get_wpx_setting_fields(){
        return new WPXtension_Setting_Fields(self::check_plugin_state($this->plugin()));
    }

    /**
     *
     * Admin Settings
     *
     */

    public function get_backend(){
        return Variation_Price_Display_Admin_Settings::instance();
    }


    /**
     *
     * Frontend 
     *
     */

    public function get_frontend(){
        return Variation_Price_Display_Front::instance();
    }


    /**
     *
     * Return all options of Variation Price Display Plugin
     *
     */
    public static function get_options(){

        $get_option = (array) get_option('variation_price_display_option') + (array) get_option('variation_price_display_option_advanced');

        $options = array(

            // ### General Settings

            'price_types' => ( !empty( $get_option['price_types'] ) ) ? $get_option['price_types'] : 'min',

            'from_before_min_price' => ( empty( $get_option['from_before_min_price'] ) ) ? 'no' : $get_option['from_before_min_price'],

            'up_to_before_max_price' => ( empty( $get_option['up_to_before_max_price'] ) ) ? 'no' : $get_option['up_to_before_max_price'],

            'custom_price_text' => ( empty( $get_option['custom_price_text'] ) ) ? __('Starts at %min_price%', 'variation-price-display') : $get_option['custom_price_text'],

            'change_price' => ( empty( $get_option['change_price'] ) ) ? 'no' : $get_option['change_price'],

            'hide_default_price' => ( empty( $get_option['hide_default_price'] ) ) ? 'no' : $get_option['hide_default_price'],

            'hide_reset_link' => ( empty( $get_option['hide_reset_link'] ) ) ? 'no' : $get_option['hide_reset_link'],

            'format_sale_price' => ( empty( $get_option['format_sale_price'] ) ) ? 'no' : $get_option['format_sale_price'],

            'wrapper_class' => ( empty( $get_option['wrapper_class'] ) ) ? '' : $get_option['wrapper_class'],

            'remove_price_class' => ( empty( $get_option['remove_price_class'] ) ) ? '' : $get_option['remove_price_class'],


            // ### Advanced Settings

            'display_condition' => ( !empty( $get_option['display_condition'] ) ) ? $get_option['display_condition'] : 'both',

            'exin_condition' => ( !empty( $get_option['exin_condition'] ) ) ? $get_option['exin_condition'] : 'none',

            'categories' => ( !empty( $get_option['categories'] ) ) ? $get_option['categories'] : array(),

            'display_variation_sku' => ( empty( $get_option['display_variation_sku'] ) ) ? 'no' : $get_option['display_variation_sku'],

            'display_discount_badge' => ( empty( $get_option['display_discount_badge'] ) ) ? 'no' : $get_option['display_discount_badge'],

            'disable_price_format_for_admin' => ( empty( $get_option['disable_price_format_for_admin'] ) ) ? 'no' : $get_option['disable_price_format_for_admin'],
            
            'disable_product_name' => ( empty( $get_option['disable_product_name'] ) ) ? 'no' : $get_option['disable_product_name'],

            'enable_for_grouped_product' => ( empty( $get_option['enable_for_grouped_product'] ) ) ? 'no' : $get_option['enable_for_grouped_product'],

            'reverse_format_sale_price' => ( empty( $get_option['reverse_format_sale_price'] ) ) ? 'no' : $get_option['reverse_format_sale_price'],

            'discount_badge_color' => ( empty( $get_option['discount_badge_color'] ) ) ? '#cf2e2e' : $get_option['discount_badge_color'],
            'discount_badge_text_color' => ( empty( $get_option['discount_badge_text_color'] ) ) ? '#ffffff' : $get_option['discount_badge_text_color'],

        );

        return (object) apply_filters( 'vpd_options', $options );
    }

    /**
     *
     * Check plugin exits or not
     *
     */

    public static function check_plugin_state( $plugin_name ){

        // echo "<h1>".$plugin_name."</h1>";

        include_once ABSPATH . 'wp-admin/includes/plugin.php';

        if (is_plugin_active( $plugin_name.'/'.$plugin_name.'.php' ) ){

            return true;

        }
        else{

            return false;

        }

    }

    /**
     *
     * Getting all categories for WooCommerce Product
     *
     */
    public static function get_categories(){
        $orderby = 'name';
        $order = 'asc';
        $hide_empty = false ;
        $cat_args = array(
            'taxonomy'   => 'product_cat',
            'orderby'    => $orderby,
            'order'      => $order,
            'hide_empty' => $hide_empty,
        );
        $product_categories = get_terms( $cat_args );
        // print_r($product_categories);
        return $product_categories;
    }
    

}