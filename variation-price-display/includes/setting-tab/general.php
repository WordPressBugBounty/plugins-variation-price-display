<section class="general" id="vpd-general-section">

    <h3><?php esc_attr_e('Variation Prices', 'variation-price-display') ?></h3>
    <p><?php esc_attr_e('Replace the WooCommerce variation price range with any other format', 'variation-price-display') ?></p>
    <table class="widefat wpx-table">

        <?php 
            
            // Price Types
            WPXtension_Setting_Fields::select(
                $options = array(
                    'tr_class' => 'alternate',
                    'label' => esc_attr__('Price types', 'variation-price-display'),
                    'ele_class' => ' price_types',
                    'value' => Variation_Price_Display::get_options()->price_types,
                    'name' => 'variation_price_display_option[price_types]',
                    'option' => apply_filters('vpd_price_type_option_list', array(
                        'option_1' => array(
                            'name' => __('Minimum Price', 'variation-price-display'),
                            'value' => 'min',
                            'need_pro' => false,
                        ),
                        'option_2' => array(
                            'name' => __('Maximum Price', 'variation-price-display'),
                            'value' => 'max',
                            'need_pro' => false,
                        ),
                        'option_3' => array(
                            'name' => __('Minimum to Maximum Price', 'variation-price-display'),
                            'value' => 'min_to_max',
                            'need_pro' => false,
                        ),
                        'option_4' => array(
                            'name' => __('Maximum to Minimum Price', 'variation-price-display'),
                            'value' => 'max_to_min',
                            'need_pro' => false,
                        ),
                        'option_5' => array(
                            'name' => Variation_Price_Display::get_options()->enable_for_grouped_product === "no" ? __('List all variation price', 'variation-price-display') : __('List all price', 'variation-price-display'),
                            'value' => 'list_variations',
                            'need_pro' => true,
                        ),
                        'option_6' => array(
                            'name' => __('Custom', 'variation-price-display'),
                            'value' => 'custom',
                            'need_pro' => true,
                        ),
                    )),
                    'note' => '',
                    'need_pro' => false,
                    'pro_exists' => Variation_Price_Display::check_plugin_state('variation-price-display-pro'),
                )
            ); 

            // Add Form
            WPXtension_Setting_Fields::checkbox(
                $options = array(
                    'tr_class' => '',
                    'label' => esc_attr__('Add From', 'variation-price-display'),
                    'ele_class' => 'from_before_min_price',
                    'value' => !empty( get_option('variation_price_display_option') ) ?  Variation_Price_Display::get_options()->from_before_min_price : 'yes',
                    'name' => 'variation_price_display_option[from_before_min_price]',
                    'default_value' => 'yes', //initially true or checked //@note: also true at get_options();
                    'checkbox_label' => __('Enable it to display <b><u>From</u></b> before Minimum Price.', 'variation-price-display'),
                    'note' => '',
                    'need_pro' => false,
                )
            ); 


            // Add UpTo
            WPXtension_Setting_Fields::checkbox(
                $options = array(
                    'tr_class' => '',
                    'label' => esc_attr__('Add Up To', 'variation-price-display'),
                    'ele_class' => 'up_to_before_max_price',
                    'value' => Variation_Price_Display::get_options()->up_to_before_max_price,
                    'name' => 'variation_price_display_option[up_to_before_max_price]',
                    'default_value' => 'yes', //initially true or checked //@note: but false at get_options();
                    'checkbox_label' => __('Enable it to display <b><u>Up To</u></b> before Maximum Price.', 'variation-price-display'),
                    'note' => '',
                    'need_pro' => false,
                )
            ); 

            // Custom Text
            WPXtension_Setting_Fields::text(
                $options = array(
                    'tr_class' => '',
                    'label' => esc_attr__('Custom Text', 'variation-price-display'),
                    'ele_class' => ' custom_price_text',
                    'value' => Variation_Price_Display::get_options()->custom_price_text,
                    'name' => 'variation_price_display_option[custom_price_text]',
                    'note' => __('<b>Some Examples:</b> <code>Starts at %min_price%</code>, <code>Starts %min_price% to %max_price%</code>.', 'variation-price-display'),
                    'note_info' => __('Display price format as you want, between two prices. <b>Note:</b> Display <b>Minimum Price</b> as <u>%min_price%</u> and <b>Maximum Price</b> as <u>%max_price%</u>.'),
                    'placeholder' => '',
                    'need_pro' => true,
                    'pro_exists' => Variation_Price_Display::check_plugin_state('variation-price-display-pro'),
                )
            ); 

            // Change Price on variation change
            WPXtension_Setting_Fields::checkbox(
                $options = array(
                    'tr_class' => 'alternate',
                    'label' => esc_attr__('Variation Price', 'variation-price-display'),
                    'value' => !empty( get_option('variation_price_display_option') ) ?  Variation_Price_Display::get_options()->change_price : 'yes',
                    'name' => 'variation_price_display_option[change_price]',
                    'default_value' => 'yes', //initially true or checked //@note: also true at get_options();
                    'checkbox_label' => __('Change price, based on selected variation(s).', 'variation-price-display'),
                    'note' => '',
                    'need_pro' => false,
                )
            ); 


            // Hide Default Price
            WPXtension_Setting_Fields::checkbox(
                $options = array(
                    'tr_class' => '',
                    'label' => esc_attr__('Hide Default Price', 'variation-price-display'),
                    'value' => !empty( get_option('variation_price_display_option') ) ? Variation_Price_Display::get_options()->hide_default_price : 'yes',
                    'name' => 'variation_price_display_option[hide_default_price]',
                    'default_value' => 'yes', //initially true or checked //@note: also true at get_options();
                    'checkbox_label' => __('Don\'t display default variation price.', 'variation-price-display'),
                    'note' => '',
                    'need_pro' => false,
                )
            ); 

            // Hide Reset Link
            WPXtension_Setting_Fields::checkbox(
                $options = array(
                    'tr_class' => 'alternate',
                    'label' => esc_attr__('Hide Reset Link', 'variation-price-display'),
                    'value' => Variation_Price_Display::get_options()->hide_reset_link,
                    'name' => 'variation_price_display_option[hide_reset_link]',
                    'default_value' => 'yes', //initially true or checked //@note: but false at get_options();
                    'checkbox_label' => __('Remove "Clear" link on single product page.', 'variation-price-display'),
                    'note' => '',
                    'need_pro' => false,
                )
            ); 

            // Format Sale Price
            WPXtension_Setting_Fields::checkbox(
                $options = array(
                    'tr_class' => '',
                    'label' => esc_attr__('Format Sale Price', 'variation-price-display'),
                    'ele_class' => 'format_sale_price',
                    'value' => Variation_Price_Display::get_options()->format_sale_price,
                    'name' => 'variation_price_display_option[format_sale_price]',
                    'default_value' => 'yes', //initially true or checked //@note: but false at get_options();
                    'checkbox_label' => __('Show Regular Price and Sale Price Format.', 'variation-price-display'),
                    'note_info' => '<b>For Example:</b> <code>From <del>$40</del> $38 </code>',
                    'need_pro' => false,
                )
            );
            // Custom Text
            WPXtension_Setting_Fields::text(
                $options = array(
                    'tr_class' => 'alternate',
                    'label' => esc_attr__('Product Wrapper Class', 'variation-price-display'),
                    'ele_class' => '',
                    'value' => Variation_Price_Display::get_options()->wrapper_class,
                    'name' => 'variation_price_display_option[wrapper_class]',
                    'note' => __('Give <code>comma (,)</code> after each target classes. <b>Examples:</b> <code>.product.product-type-variable</code>.', 'variation-price-display'),
                    'note_info' => __('Keep blank, if you haven\'t any issues with the price changing. This field is for fixing price changing compatibility issue.', 'variation-price-display'),
                    'placeholder' => '.product.product-type-variable',
                    'need_pro' => false,
                    'tag' => esc_attr__('New', 'variation-price-display'),
                )
            ); 
            // Custom Text
            WPXtension_Setting_Fields::text(
                $options = array(
                    'tr_class' => '',
                    'label' => esc_attr__('Remove Price Class', 'variation-price-display'),
                    'ele_class' => '',
                    'value' => Variation_Price_Display::get_options()->remove_price_class,
                    'name' => 'variation_price_display_option[remove_price_class]',
                    'note' => __('Give <code>comma (,)</code> after each target classes. <b>Examples:</b> <code>.df-product-inner-wrap .df-product-price.price, .product-inner-wrap .price</code>.', 'variation-price-display'),
                    'note_info' => __('Keep blank, if you haven\'t any issues with the price changing. This field is for fixing price changing compatibility issue in the product description/singe product page.', 'variation-price-display'),
                    'placeholder' => '.product-inner-wrap .price',
                    'need_pro' => false,
                )
            ); 

        ?>
    </table>

</section>