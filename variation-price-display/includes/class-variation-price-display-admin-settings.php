<?php

class Variation_Price_Display_Admin_Settings{
	
	protected static $_instance = null;

    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function __construct(){
        add_action( 'admin_menu', array( __CLASS__, 'submenu' ), 99 );
        add_action( 'admin_init', array( __CLASS__, 'register_plugin_setting' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );
        // Tab Sections
        add_action('vpd_setting_tab_content', array( __CLASS__, 'tab_contents' ), 10, 2);
        // Settings Link
        add_filter( 'plugin_action_links_variation-price-display/variation-price-display.php', array( $this, 'settings_link') );
        // Plugin row meta link
        add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 4 );
        // Clear Settings
        add_action('vpd_layout_start', array( $this, 'reset_setting' ) );
        // Update Settings
        add_action( 'admin_init', array( $this, 'upgrade_option' ) );
    }

    public static function submenu( ){

    	add_submenu_page('wpxtension', 'Variation Price Display', 'Price Display', 'manage_options', 'variation-price-display', array( __CLASS__, 'menu_page' ) );
    }

    public static function menu_page(){
        if ( is_file( plugin_dir_path( VARIATION_PRICE_DISPLAY_PLUGIN_FILE ) . 'includes/wpxtension/wpx-sidebar.php' ) ) {
            include_once plugin_dir_path( VARIATION_PRICE_DISPLAY_PLUGIN_FILE ) . 'includes/wpxtension/wpx-sidebar.php';
        }
        if ( is_file( plugin_dir_path( VARIATION_PRICE_DISPLAY_PLUGIN_FILE ) . 'includes/layout.php' ) ) {
            include_once plugin_dir_path( VARIATION_PRICE_DISPLAY_PLUGIN_FILE ) . 'includes/layout.php';
        }
    }

    public static function get_setting(){
    	return get_option( 'product_share_option' );
    }

    public static function tab_contents( $plugin_name, $curTab ){

        if( 'variation-price-display' !==  $plugin_name ){
            return;
        }

        if( 'advanced' === $curTab ){
            settings_fields( 'variation-price-display-group_adavanced' );
            do_settings_sections( 'variation-price-display-group_adavanced' );
            require_once dirname( __FILE__ ) . '/setting-tab/advanced.php';
        }
        if( '' === $curTab || null === $curTab ){
            settings_fields( 'variation-price-display-group' );
            do_settings_sections( 'variation-price-display-group' );
            require_once dirname( __FILE__ ) . '/setting-tab/general.php';
        }
    }

    public static function register_plugin_setting(){
    	register_setting( 'variation-price-display-group', 'variation_price_display_option' );
        register_setting( 'variation-price-display-group_adavanced', 'variation_price_display_option_advanced' );
        register_setting( 'variation-price-display-group_license', 'variation_price_display_license' );
    }

    public function admin_assets() {

        // @Note: Checking if `SCRIPT_DEBUG` is defined and `true`
        $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

        $admin_settings_nonce = wp_create_nonce( 'vpd-admin-settings-nonce' );

        if( wp_verify_nonce( $admin_settings_nonce, 'vpd-admin-settings-nonce' ) ){

            if ( isset( $_GET['page'] ) && ! empty( $_GET['page'] ) && 'variation-price-display' === $_GET['page'] ) {
                wp_enqueue_style('vpd-admin', plugins_url('admin/css/backend'. $suffix .'.css', VARIATION_PRICE_DISPLAY_PLUGIN_FILE), array(), variation_price_display()->version(), 'all');
                wp_enqueue_style('wpxtension-admin', plugins_url('includes/wpxtension/wpxtension-admin'. $suffix .'.css', VARIATION_PRICE_DISPLAY_PLUGIN_FILE), array(), variation_price_display()->version(), 'all');
                wp_style_add_data( 'wpxtension-admin', 'rtl', 'replace' );
                
                wp_enqueue_style( 'wp-color-picker' ); 
                wp_enqueue_script('vpd-admin', plugins_url('admin/js/backend'.$suffix .'.js', VARIATION_PRICE_DISPLAY_PLUGIN_FILE), array('jquery','wp-color-picker'), variation_price_display()->version(), true);
                wp_localize_script( 'vpd-admin', 'vpd_admin_object',
                    array( 
                        'priceType' => Variation_Price_Display::get_options()->price_types,
                        'ExInCondition' => Variation_Price_Display::get_options()->exin_condition,
                    )
                );

                // Select2 Style & Script
                wp_enqueue_style('wpxtension-select2', plugins_url('admin/css/select2.min.css', VARIATION_PRICE_DISPLAY_PLUGIN_FILE), array(), variation_price_display()->version(), 'all');
                wp_enqueue_script('wpxtension-select2', plugins_url('admin/js/select2.min.js', VARIATION_PRICE_DISPLAY_PLUGIN_FILE), array('jquery'), variation_price_display()->version(), true);
            }
        }
    }

    public function settings_link($links) { 
        // Build and escape the URL.
        $url = esc_url( add_query_arg(
            'page',
            'variation-price-display',
            get_admin_url() . 'admin.php'
        ) );
        // Create the link.
        $settings_link = "<a href='$url'>" . __( 'Settings', 'variation-price-display' ) . '</a>';
        
        // Adds the link to the begining of the array.
        array_unshift( $links, $settings_link );

        if( !Variation_Price_Display::check_plugin_state('variation-price-display-pro') ){
            $pro_link = "<a style='font-weight: bold; color: #8012f9;' href='https://wpxtension.com/product/variation-price-display-for-woocommerce/' target='_blank'>" . __( 'Go Premium' ) . '</a>';
            array_push( $links, $pro_link );
        }
        return $links; 
    }

    /**
    * ====================================================
    * Plugin row link for plugin listing page
    * ====================================================
    **/

    public function plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {

        if ( strpos( $plugin_file, 'variation-price-display.php' ) !== false ) {

            $new_links = array(
                'ticket' => '<a href="https://wpxtension.com/submit-a-ticket/" target="_blank" style="font-weight: bold; color: #8012f9;">Help & Support</a>',
                'doc' => '<a href="https://wpxtension.com/doc-category/variation-price-display-for-woocommerce/" target="_blank">Documentation</a>'
            );
             
            $plugin_meta = array_merge( $plugin_meta, $new_links );

        }
         
        return $plugin_meta;
    }

    /**
    * ====================================================
    * Reset Conditions for settings
    * ====================================================
    **/
    public function reset_setting(){

        // Condition starts from here

        if( isset( $_GET['action'] ) && 'reset' === $_GET['action'] ){

            //In our file that handles the request, verify the nonce.
            if ( isset( $_REQUEST['_wpnonce'] ) && ! wp_verify_nonce( sanitize_key( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'vpd-settings' ) ) {
                die( esc_html__( 'Security check', 'variation-price-display' ) ); 
            } else {
                
                if( isset( $_GET['tab'] ) && 'advanced' === $_GET['tab'] ){
                    delete_option('variation_price_display_option_advanced');
                    wp_safe_redirect( admin_url( 'admin.php?page=variation-price-display&tab=' . sanitize_key( wp_unslash( $_GET['tab'] ) ) ) );
                    exit();
                }
                elseif( isset( $_GET['tab'] ) && 'license' === $_GET['tab'] ){
                    delete_option('variation_price_display_license');
                    wp_safe_redirect( admin_url( 'admin.php?page=variation-price-display&tab=' . sanitize_key( wp_unslash( $_GET['tab'] ) ) ) );
                    exit();
                }
                else{
                    delete_option('variation_price_display_option');
                    wp_safe_redirect( admin_url( 'admin.php?page=variation-price-display' ) );
                    exit();
                }

            }

        }
        
    }


    public function upgrade_option(){

        // General Tab Option

        $general_options = array();

        if( false !== get_option('vpd_price_types') ){
            $general_options['price_types'] = get_option('vpd_price_types');
        }
        if( false !== get_option('vpd_from_before_min_price') ){
            $general_options['from_before_min_price'] = get_option('vpd_from_before_min_price');
        }
        if( false !== get_option('vpd_up_to_before_max_price') ){
            $general_options['up_to_before_max_price'] = get_option('vpd_up_to_before_max_price');
        }
        if( false !== get_option('vpd_custom_price_text') ){
            $general_options['custom_price_text'] = get_option('vpd_custom_price_text');
        }
        if( false !== get_option('vpd_change_price') ){
            $general_options['change_price'] = get_option('vpd_change_price');
        }
        if( false !== get_option('vpd_hide_default_price') ){
            $general_options['hide_default_price'] = get_option('vpd_hide_default_price');
        }
        if( false !== get_option('vpd_hide_reset_link') ){
            $general_options['hide_reset_link'] = get_option('vpd_hide_reset_link');
        }
        if( false !== get_option('vpd_format_sale_price') ){
            $general_options['format_sale_price'] = get_option('vpd_format_sale_price');
        }

        if( false === get_option('variation_price_display_option') ){
            add_option('variation_price_display_option', $general_options);
        }

        // Advanced Tab Option

        $advanved_options = array();

        if( false !== get_option('vpd_display_condition') ){
            $advanved_options['display_condition'] = get_option('vpd_display_condition');
        }
        if( false !== get_option('vpd_display_variation_sku') ){
            $advanved_options['display_variation_sku'] = get_option('vpd_display_variation_sku');
        }
        if( false !== get_option('vpd_display_discount_badge') ){
            $advanved_options['display_discount_badge'] = get_option('vpd_display_discount_badge');
        }
        if( false !== get_option('vpd_disable_price_format_for_admin') ){
            $advanved_options['disable_price_format_for_admin'] = get_option('vpd_disable_price_format_for_admin');
        }

        if( false === get_option('variation_price_display_option_advanced') ){
            add_option('variation_price_display_option_advanced', $advanved_options);
        }

        // License Tab Option

        if( false !== get_option('vpd_license_key_text') && false === get_option('variation_price_display_license') ){
            add_option('variation_price_display_license', get_option('vpd_license_key_text'));
        }

        ##################################################
        // Deleting old options General Tab
        ##################################################
        if( false !== get_option('vpd_price_types') ){
            delete_option('vpd_price_types');
        }
        if( false !== get_option('vpd_from_before_min_price') ){
            delete_option('vpd_from_before_min_price');
        }
        if( false !== get_option('vpd_up_to_before_max_price') ){
            delete_option('vpd_up_to_before_max_price');
        }
        if( false !== get_option('vpd_custom_price_text') ){
            delete_option('vpd_custom_price_text');
        }
        if( false !== get_option('vpd_change_price') ){
            delete_option('vpd_change_price');
        }
        if( false !== get_option('vpd_hide_default_price') ){
            delete_option('vpd_hide_default_price');
        }
        if( false !== get_option('vpd_hide_reset_link') ){
            delete_option('vpd_hide_reset_link');
        }
        if( false !== get_option('vpd_format_sale_price') ){
            delete_option('vpd_format_sale_price');
        }


        // Deleting old options Advanced Tab

        if( false !== get_option('vpd_display_condition') ){
            delete_option('vpd_display_condition');
        }
        if( false !== get_option('vpd_display_variation_sku') ){
            delete_option('vpd_display_variation_sku');
        }
        if( false !== get_option('vpd_display_discount_badge') ){
            delete_option('vpd_display_discount_badge');
        }
        if( false !== get_option('vpd_disable_price_format_for_admin') ){
            delete_option('vpd_disable_price_format_for_admin');
        }

        // Deleting old options License Tab
        if( false !== get_option('vpd_license_key_text') ){
            delete_option('vpd_license_key_text');
        }

    }

}