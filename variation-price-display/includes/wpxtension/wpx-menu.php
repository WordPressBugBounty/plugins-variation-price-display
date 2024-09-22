<?php
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WPXtension_Menu' ) ) {
	class WPXtension_Menu {

		protected static $_instance = null;

		public static function instance() {
	        if ( is_null( self::$_instance ) ) {
	            self::$_instance = new self();
	        }

	        return self::$_instance;
	    }

	    protected static $_plugins = array(
			'variation-price-display'             => array(
				'name' => 'Variation Price Display Range for WooCommerce',
				'slug' => 'variation-price-display',
				'file' => 'variation-price-display.php'
			),
			'product-variant-table-for-woocommerce'         => array(
				'name' => 'Product Variation Table for WooCommerce',
				'slug' => 'product-variant-table-for-woocommerce',
				'file' => 'product-variant-table-for-woocommerce.php'
			),
			'fast-cart'            => array(
				'name' => 'Fast Cart for WooCommerce',
				'slug' => 'fast-cart',
				'file' => 'fast-cart.php'
			),
			'product-share'            => array(
				'name' => 'Product Share for WooCommerce',
				'slug' => 'product-share',
				'file' => 'product-share.php'
			),
		);

		function __construct() {
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_action( 'admin_menu', array( $this, 'useful_plugins_menu' ), 100 );
		}

		function admin_menu() {

			$wpx_icon = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxNiIgaGVpZ2h0PSIxNiIgZmlsbD0iY3VycmVudENvbG9yIiBjbGFzcz0iYmkgYmktcGx1Zy1maWxsIiB2aWV3Qm94PSIwIDAgMTYgMTYiPgogIDxwYXRoIGQ9Ik02IDBhLjUuNSAwIDAgMSAuNS41VjNoM1YuNWEuNS41IDAgMCAxIDEgMFYzaDFhLjUuNSAwIDAgMSAuNS41djNBMy41IDMuNSAwIDAgMSA4LjUgMTBjLS4wMDIuNDM0LS4wMS44NDUtLjA0IDEuMjItLjA0MS41MTQtLjEyNiAxLjAwMy0uMzE3IDEuNDI0YTIuMDgzIDIuMDgzIDAgMCAxLS45NyAxLjAyOEM2LjcyNSAxMy45IDYuMTY5IDE0IDUuNSAxNGMtLjk5OCAwLTEuNjEuMzMtMS45NzQuNzE4QTEuOTIyIDEuOTIyIDAgMCAwIDMgMTZIMmMwLS42MTYuMjMyLTEuMzY3Ljc5Ny0xLjk2OEMzLjM3NCAxMy40MiA0LjI2MSAxMyA1LjUgMTNjLjU4MSAwIC45NjItLjA4OCAxLjIxOC0uMjE5LjI0MS0uMTIzLjQtLjMuNTE0LS41NS4xMjEtLjI2Ni4xOTMtLjYyMS4yMy0xLjA5LjAyNy0uMzQuMDM1LS43MTguMDM3LTEuMTQxQTMuNSAzLjUgMCAwIDEgNCA2LjV2LTNhLjUuNSAwIDAgMSAuNS0uNWgxVi41QS41LjUgMCAwIDEgNiAweiIvPgo8L3N2Zz4=';

			$hook = add_menu_page(
				'WPXtension',
				'WPXtension',
				'manage_options',
				'wpxtension',
				array( $this, 'welcome_content' ),
				$wpx_icon,
				26
			);
			add_submenu_page( 'wpxtension', 'WPX About', 'Useful Plugins', 'manage_options', 'wpxtension' );
			// remove the "main" submenue page
		    remove_submenu_page('wpxtension', 'wpxtension');
		    // tell `_wp_menu_output` not to use the submenu item as its link
		    add_filter("submenu_as_parent_{$hook}", '__return_false');
		}

		function useful_plugins_menu() {

			$search = array(
                's'    	=> 'wpxteam',
                'tab'   => 'search',
                'type'	=> 'author'
            );

            $permalink = add_query_arg( $search, admin_url( 'plugin-install.php' ) );

			add_submenu_page( 'wpxtension', 'Useful Plugins', 'Useful Plugins', 'manage_options', $permalink );
		}

	}
}