<section class="advnaced" id="vpd-advanced-section">

<h3><?php esc_attr_e('Advanced Settings', 'variation-price-display'); ?></h3>
<p><?php esc_attr_e('The advanced options to control your price format', 'variation-price-display') ?></p>

<table class="widefat wpx-table">

    <?php 

        // Display Condition
        WPXtension_Setting_Fields::select(
            $options = array(
                'tr_class' => 'alternate',
                'label' => esc_attr__('Display Condition', 'variation-price-display'),
                'ele_class' => ' display_condition',
                'value' => Variation_Price_Display::get_options()->display_condition,
                'name' => 'variation_price_display_option_advanced[display_condition]',
                'option' => apply_filters('vpd_display_conditio_html', array(
                    'option_1' => array(
                        'name' => __( 'Shop/Archive Page ', 'variation-price-display' ),
                        'value' => 'shop',
                        'need_pro' => true,
                    ),
                    'option_2' => array(
                        'name' => __( 'Single Product/Product Description Page ', 'variation-price-display' ),
                        'value' => 'single',
                        'need_pro' => true,
                    ),
                    'option_3' => array(
                        'name' => __( 'Both Shop and Single Product Page ', 'variation-price-display' ),
                        'value' => 'both',
                        'need_pro' => true,
                    ),
                )),
                'note' => '',
                'need_pro' => true,
                'pro_exists' => Variation_Price_Display::check_plugin_state('variation-price-display-pro'),
            )
        ); 

         // Exclude/Include Condition
        WPXtension_Setting_Fields::select(
            $options = array(
                'tr_class' => '',
                'label' => esc_attr__('Exclude/Include Condition', 'variation-price-display'),
                'ele_class' => ' exin_condition',
                'value' => Variation_Price_Display::get_options()->exin_condition,
                'name' => 'variation_price_display_option_advanced[exin_condition]',
                'option' => apply_filters('vpd_display_conditio_html', array(
                    'option_1' => array(
                        'name' => __( 'None ', 'variation-price-display' ),
                        'value' => 'none',
                        'need_pro' => true,
                    ),
                    'option_2' => array(
                        'name' => __( 'Exclude Categories ', 'variation-price-display' ),
                        'value' => 'exclude',
                        'need_pro' => true,
                    ),
                    'option_3' => array(
                        'name' => __( 'Include Categories ', 'variation-price-display' ),
                        'value' => 'include',
                        'need_pro' => true,
                    ),
                )),
                'note' => '',
                'need_pro' => true,
                'pro_exists' => Variation_Price_Display::check_plugin_state('variation-price-display-pro'),
            )
        ); 

        //============== Select Categories ==============

        // Disable VPD based on categories
        
        $cat_options = array();
        $i = 0;
        
        foreach( Variation_Price_Display::get_categories() as $cat ){ $i++;
            $cat_options += array(
                'option_'.$i => array(
                    'name' => $cat->name,
                    'value' => $cat->term_id,
                    'need_pro' => true,
                )
            );
        }
        // print_r($cat_options);

        WPXtension_Setting_Fields::multiselect(
            $options = array(
                'tr_class' => 'alternate',
                'label' => esc_attr__('Select Categories', 'variation-price-display'),
                'ele_class' => ' categories wpx-multiselect',
                'value' => Variation_Price_Display::get_options()->categories,
                'name' => 'variation_price_display_option_advanced[categories][]',
                'option' => apply_filters('vpd_categories', $cat_options),
                'note' => '',
                'need_pro' => true,
                'pro_exists' => Variation_Price_Display::check_plugin_state('variation-price-display-pro'),
            )
        ); 
        //============== Select Categories ==============

        // SKU with variation name
        WPXtension_Setting_Fields::checkbox(
            $options = array(
                'tr_class' => '',
                'label' => esc_attr__('SKU with variation name', 'variation-price-display'),
                'ele_class' => 'display_variation_sku',
                'value' => !empty( get_option('variation_price_display_option_advanced') ) ? Variation_Price_Display::get_options()->display_variation_sku : 'no',
                'name' => 'variation_price_display_option_advanced[display_variation_sku]',
                'default_value' => 'yes', //true or checked
                'checkbox_label' => __('Enable it to display <b><u>SKU</u></b>, if <b>Price Types:</b> <i>List all variation price/List all price</i>.', 'variation-price-display'),
                'note' => '',
                'note_info' => __('<b>For Example:</b> <code>Hoodie – Blue, Yes (woo-hoodie-blue-logo) – <del>$40.00</del> $38.00</code>.', 'variation-price-display'),
                'need_pro' => true,
                'pro_exists' => Variation_Price_Display::check_plugin_state('variation-price-display-pro'),
            )
        ); 

        // Display discount badge
        WPXtension_Setting_Fields::checkbox(
            $options = array(
                'tr_class' => 'alternate',
                'label' => esc_attr__('Display discount badge', 'variation-price-display'),
                'ele_class' => 'display_discount_badge',
                'value' => !empty( get_option('variation_price_display_option_advanced') ) ? Variation_Price_Display::get_options()->display_discount_badge : 'yes',
                'name' => 'variation_price_display_option_advanced[display_discount_badge]',
                'default_value' => 'yes', //true or checked
                'checkbox_label' => __('Enable it to display <b><u>Discount Badge</u></b>'),
                'note' => '',
                'note_info' => __('<b>Note:</b> To get it to work with <b>[Minimum/Maximum Price]</b>, please enable <b>Format Sale Price</b> option from <b>General Tab</b>. This option will also work with <b>List all variation price</b> but will not work if Grouped Child product is not a variable product.', 'variation-price-display'),
                'need_pro' => true,
                'pro_exists' => Variation_Price_Display::check_plugin_state('variation-price-display-pro'),
            )
        ); 

        // Badge Color
        WPXtension_Setting_Fields::color(
            $options = array(
                'tr_class' => 'new',
                'label' => esc_attr__('Discount Badge Color', 'variation-price-display'),
                'value' => Variation_Price_Display::get_options()->discount_badge_color,
                'name' => 'variation_price_display_option_advanced[discount_badge_color]',
                'default_value' => Variation_Price_Display::get_options()->discount_badge_color,
                'note' => '',
                'need_pro' => true,
                'tag' => esc_attr__('New', 'product-share'),
                'pro_exists' => Variation_Price_Display::check_plugin_state('variation-price-display-pro'),
                'tag' => esc_attr__('New', 'variation-price-display'),
            )
        ); 

        // Badge Text Color
        WPXtension_Setting_Fields::color(
            $options = array(
                'tr_class' => 'alternate new',
                'label' => esc_attr__('Discount Badge Text Color', 'variation-price-display'),
                'value' => Variation_Price_Display::get_options()->discount_badge_text_color,
                'name' => 'variation_price_display_option_advanced[discount_badge_text_color]',
                'default_value' => Variation_Price_Display::get_options()->discount_badge_text_color,
                'note' => '',
                'need_pro' => true,
                'tag' => esc_attr__('New', 'product-share'),
                'pro_exists' => Variation_Price_Display::check_plugin_state('variation-price-display-pro'),
                'tag' => esc_attr__('New', 'variation-price-display'),
            )
        ); 


        // Disable Price for Admin
        WPXtension_Setting_Fields::checkbox(
            $options = array(
                'tr_class' => '',
                'label' => esc_attr__('Disable Price for Admin', 'variation-price-display'),
                'ele_class' => 'disable_price_format_for_admin',
                'value' => !empty( get_option('variation_price_display_option_advanced') ) ? Variation_Price_Display::get_options()->disable_price_format_for_admin : 'no',
                'name' => 'variation_price_display_option_advanced[disable_price_format_for_admin]',
                'default_value' => 'yes', //true or checked
                'checkbox_label' => __('Disable Price Format for the Admin.'),
                'note' => '',
                'note_info' => __('<b>Note:</b> By enabling this option, Admin can see the default price range while logged in.', 'variation-price-display'),
                'need_pro' => true,
                'pro_exists' => Variation_Price_Display::check_plugin_state('variation-price-display-pro'),
            )
        ); 

        // Disable product name from the list all variation

        WPXtension_Setting_Fields::checkbox(
            $options = array(
                'tr_class' => 'alternate',
                'label' => esc_attr__('Disable Product Name', 'variation-price-display'),
                'ele_class' => 'disable_product_name',
                'value' => !empty( get_option('variation_price_display_option_advanced') ) ? Variation_Price_Display::get_options()->disable_product_name : 'no',
                'name' => 'variation_price_display_option_advanced[disable_product_name]',
                'default_value' => 'yes', //true or checked
                'checkbox_label' => __('Disable Product Name When Price Type is <b><u>List All Variation</u></b>.'),
                'note' => '',
                'note_info' => __('<b>Note:</b> By enabling this option, Product Name will be removed when you have selected <b>List all variation price</b>.', 'variation-price-display'),
                'need_pro' => true,
                'pro_exists' => Variation_Price_Display::check_plugin_state('variation-price-display-pro'),
                'tag' => esc_attr__('New', 'variation-price-display'),
            )
        ); 

        // Reverse Format Sale Price
        /*@note: Display this option if the Format Sale Price is checked or enabled*/
        if( 'yes' === Variation_Price_Display::get_options()->format_sale_price ){
            WPXtension_Setting_Fields::checkbox(
                $options         = array(
                    'tr_class'       => '',
                    'label'          => esc_attr__('Reverse Format Sale Price', 'variation-price-display'),
                    'ele_class'      => 'reverse_format_sale_price',
                    'value'          => Variation_Price_Display::get_options()->reverse_format_sale_price,
                    'name'           => 'variation_price_display_option_advanced[reverse_format_sale_price]',
                    'default_value'  => 'yes', //initially true or checked //@note: but false at get_options();
                    'checkbox_label' => __('Change the format sale price in different Format.', 'variation-price-display'),
                    'note'           => __('Note: It will not work for List all varition price/ List all price', 'variation-price-display'),
                    'note_info'      => '<b>For Example:</b> <code>From $38 <del>$40</del> </code>',
                    'need_pro'       => true,
                    'pro_exists'     => Variation_Price_Display::check_plugin_state('variation-price-display-pro'),
                    'tag'            => esc_attr__('New', 'variation-price-display'),
                )
            );
        }

        // Enable Price Display for Grouped Product
        WPXtension_Setting_Fields::checkbox(
            $options = array(
                'tr_class' => 'alternate',
                'label' => esc_attr__('Enable for Grouped Product', 'variation-price-display'),
                'ele_class' => 'enable_for_grouped_product',
                'value' => !empty( get_option('variation_price_display_option_advanced') ) ? Variation_Price_Display::get_options()->enable_for_grouped_product : 'no',
                'name' => 'variation_price_display_option_advanced[enable_for_grouped_product]',
                'default_value' => 'yes', //true or checked
                'checkbox_label' => __('Enable Price Display for Grouped Product type.'),
                'note' => '',
                'note_info' => __('<b>Note:</b> By enabling this option, Price settings (Price types) will be applied on Grouped Products.', 'variation-price-display'),
                'need_pro' => true,
                'pro_exists' => Variation_Price_Display::check_plugin_state('variation-price-display-pro'),
                'tag' => esc_attr__('New', 'variation-price-display'),
            )
        );


    ?>

</table>


</section>