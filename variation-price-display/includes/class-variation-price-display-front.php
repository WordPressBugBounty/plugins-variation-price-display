<?php 

class Variation_Price_Display_Front{

	protected static $_instance = null;

    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function __construct(){

    	$this->hooks();
    	do_action( 'variation_price_display_frontend_loaded', $this );

    }

    public function hooks(){
    	// Asset Load
    	add_action( 'wp_enqueue_scripts', array( $this, 'front_asset' ) );
    	add_filter( 'body_class', array( $this, 'adding_body_class' ) );
    	add_filter( 'woocommerce_variable_price_html', array( $this, 'get_price_html' ), 10, 2 );
    	add_filter( 'woocommerce_reset_variations_link', array( $this, 'remove_reset_link' ), 20, 1 );
    	add_filter( 'woocommerce_available_variation', array( $this, 'rewrite_woocommerce_available_variation' ), 99, 3 );
    }

    public function front_asset(){

    	// @Note: Checking if `SCRIPT_DEBUG` is defined and `true`
    	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

    	// Disable scripts while needed
    	if( apply_filters( 'disable_vpd_scripts', false ) ) {
			return;
		}

		wp_enqueue_style('vpd-public', plugins_url('public/css/public'.$suffix.'.css', VARIATION_PRICE_DISPLAY_PLUGIN_FILE), array(), variation_price_display()->version(), 'all');

		wp_enqueue_script('vpd-public', plugins_url('public/js/public'.$suffix.'.js', VARIATION_PRICE_DISPLAY_PLUGIN_FILE), array('jquery'), variation_price_display()->version(), true);

		wp_localize_script( 'vpd-public', 'vpd_public_object',
            array( 
                'changeVariationPrice' => !empty( get_option('variation_price_display_option') ) ? Variation_Price_Display::get_options()->change_price : 'yes',
                'hideDefaultPrice' => !empty( get_option('variation_price_display_option') ) ? Variation_Price_Display::get_options()->hide_default_price : 'yes',
                'wrapperClass' => Variation_Price_Display::get_options()->wrapper_class,
                'removePriceClass' => Variation_Price_Display::get_options()->remove_price_class,
            )
        );
    	
    }

    public function adding_body_class( $classes ){
    	if( is_product() || is_shop() ) $classes[] = apply_filters( 'wpx_body_class_for_variation-price-display', 'vpd-loaded' );
    	return $classes;
    }

    public function get_price_html( $price, $product ){

		// $product = wc_get_product( get_the_ID() );

		// Disable VPD if same price for all variations

		$variation_prices = $product->get_variation_prices( true );

	    $count  = (int) count( array_unique( $variation_prices['price'] ));

		if( $count === apply_filters('vpd_variation_same_price_count', 1) ){

			return $price;

		}

		// Disable VPD filter

		if( apply_filters( 'disable_vpd_price_format', false, $price, $product ) ){

			return $price;

		}
		
		if($product->is_type( 'variable' )):
		
			$price_types = Variation_Price_Display::get_options()->price_types;
			$from_before_min_price = !empty( get_option('variation_price_display_option') ) ?  Variation_Price_Display::get_options()->from_before_min_price : 'yes';
			$up_to_before_max_price = Variation_Price_Display::get_options()->up_to_before_max_price;
			$format_sale_price = Variation_Price_Display::get_options()->format_sale_price;

			switch ($price_types) {

			  case "min":

			  	$before_min_price = ( $from_before_min_price === 'yes' ) ? __('From&nbsp;', 'variation-price-display') : '';

			  	// $min_price = wc_price( $product->get_variation_price( 'min', true ) );
			  	$min_price = $this->format_price( $format_sale_price, 'min', $product );

				$prices = apply_filters( 'vpd_prefix_min_price', $before_min_price ) . $min_price;
				
			    break;

			  case "max":

			  	$before_max_price = ( $up_to_before_max_price === 'yes' ) ? __('Up&nbsp;To&nbsp;', 'variation-price-display') : '';

			  	$max_price = $this->format_price( $format_sale_price, 'max', $product );

				$prices = apply_filters( 'vpd_prefix_max_price', $before_max_price ) . $max_price;

			    break;

			  case "max_to_min":

			  	if( $product->get_variation_price( 'max', true ) === $product->get_variation_price( 'min', true ) ){

			  		$prices = wc_price( $product->get_variation_price( 'max', true ) );

			  	}
			  	else{

			  		$prices = wc_format_price_range($product->get_variation_price( 'max', true ) , $product->get_variation_price( 'min', true ) );

			  	}

			    break;

			  default:

			  	if( $product->get_variation_price( 'max', true ) === $product->get_variation_price( 'min', true ) ){

			  		$prices = wc_price( $product->get_variation_price( 'min', true ) );

			  	}
			  	else{

			  		// $prices = wc_format_price_range($product->get_variation_price( 'min', true ) , $product->get_variation_price( 'max', true ) );
			  		$prices = $price;
			  		
			  	}

			}

			$vpd_price = apply_filters( 'vpd_woocommerce_variable_price_html', $prices . $product->get_price_suffix(), $product, $price, $price_types );

			return $vpd_price;
		
		else:

			return $price;

		endif;
	}

	// Format Price method
    public function format_price( $format, $type, $product  ){

		switch ( $format ) {

		  case "yes":

		  	if( $product->get_variation_regular_price( $type, true ) !== $product->get_variation_sale_price( $type, true ) ){

		  		/**
		  		 * { Hook added for format sale price option }
		  		 * 
		  		 * @hook: `vpd_format_sale_price_display`
		  		 *
		  		 * @var        callable
		  		 */
				$formatted_price =  apply_filters( 'vpd_format_sale_price_display', 
					wc_format_sale_price( wc_price( $product->get_variation_regular_price( $type, true ) ), wc_price( $product->get_variation_sale_price( $type, true ) ) ),
					$product->get_variation_regular_price( $type, true ),
					$product->get_variation_sale_price( $type, true )
				);
			}
			else{

				$formatted_price = wc_price( $product->get_variation_price( $type, true ) );

			}

			$price = apply_filters( 'vpd_formatted_price', $formatted_price, $type, $product );

			break;

		  default:

		  	$formatted_price = wc_price( $product->get_variation_price( $type, true ) );

			$price = apply_filters( 'vpd_non_formatted_price', $formatted_price, $type, $product );

		}

		return apply_filters('vpd_format_price_fiter', $price, $type, $product);
	}


	// Reset "Clear" link control

	public function remove_reset_link( $link ){

		if ( Variation_Price_Display::get_options()->hide_reset_link === "no" ){
			return $link;
		}

		return false;

	}

	// Pushing price range of product inside `woocommerce_available_variation`

	public function rewrite_woocommerce_available_variation( $default, $class, $variation ){

		// Getting parent product id by variation id
		$product_id = wp_get_post_parent_id( $variation->get_id() );

		// Getting parent product instance
		$parent_product = wc_get_product( $product_id );

		// Pushing the initial price [if WC_Product class initialized]
		$default['vpd_init_price'] = ( $parent_product != null || $parent_product != false ) ? $parent_product->get_price_html() : '';

		return apply_filters( 'vpd_woocommerce_available_variation', $default, $class, $variation );

	}


}