( function ( $ ) {

    // Main Script
    var vpdInit = function(){
        // Getting object from localization
        vpdPublicObject = vpd_public_object;

        var singleVariation, 
            priceContainer, 
            initPrice, 
            prevPrice, 
            vpdPublicObject, 
            product_wrapper, 
            default_price_class;

        default_price_class =   vpdPublicObject.defaultPriceClass ? 
                                vpdPublicObject.defaultPriceClass : 
                                '.single_variation_wrap .woocommerce-variation-price';

        product_wrapper =   vpdPublicObject.wrapperClass ? 
                            vpdPublicObject.wrapperClass : 
                            '.product.product-type-variable';

        // initPrice = prevPrice = $(product_wrapper).find('.price').html();

        // singleVariation = $('.single-product .product-type-variable .single_variation_wrap .single_variation');

        // Triggering the `show_variation` event
        $(product_wrapper).on("show_variation", function (event, variation) {
            // console.log(variation);
            var curPice = $(this).find('.price').html()
            if( !variation ){
                console.info('VPD Info: Variation data not found!');
            }
            else{
                // Getting variation price
                variationPrice = variation.price_html;
                initPrice = variation.vpd_init_price;
                // Checking price_html is Empty string, undefined, null or not
                if( variation.price_html ){
                    // Passing variation price to the function
                    $(document.body).trigger('vpd_show_variation_price', [ variationPrice, $(this), initPrice, 'show_variation' ]);
                    hideDefaultPrice();
                }
            }
        });

        // Triggering `hide_variation` event
        $(product_wrapper).on("hide_variation", function(event, variation) { 
            // changePrice( initPrice, $(this), initPrice );
            $(document.body).trigger('vpd_show_variation_price', [ initPrice, $(this), initPrice, 'hide_variation' ]);
        });

        // Init the vpd_show_variation_price trigger
        $(document.body).on("vpd_show_variation_price", function( event, price, variation_wrapper, init_Price ){
            changePrice( price, variation_wrapper, init_Price );
        });

        // Function to run- on changing the variation dropdown
        function changePrice(variationPrice, priceContainer, init_Price, eventName){
            // console.log(variationPrice);
            var priceContainer2;

            if( vpdPublicObject.changeVariationPrice === "no" ) return;

            if (prevPrice === variationPrice) return;

            if( vpdPublicObject.removePriceClass !== "" ){
                priceContainer2 = priceContainer.find('.price, .wp-block-woocommerce-product-price')
                            .not('.related .price, .upsell .price, .wp-block-woocommerce-related-products .wp-block-woocommerce-product-price')
                            .not(vpdPublicObject.removePriceClass);
            }
            else{
                priceContainer2 = priceContainer.find('.price, .wp-block-woocommerce-product-price')
                            .not('.related .price, .upsell .price, .wp-block-woocommerce-related-products .wp-block-woocommerce-product-price');
            }
            // Animation Speed `vpdPublicObject.animationSpeed`. parseInt used for converting it to integer.
            priceContainer2.fadeOut( parseInt( vpdPublicObject.animationSpeed ), function () {

                $(document).trigger('vpd_before_price_fadein', [ variationPrice, priceContainer, init_Price, eventName ]);

                priceContainer2.html(variationPrice).fadeIn( parseInt( vpdPublicObject.animationSpeed ) );
                prevPrice = variationPrice;

                $(document).trigger('vpd_after_price_fadein', [ variationPrice, priceContainer, init_Price, eventName ]);

            });

            $(document).trigger('vpd_after_price_changed', [ variationPrice, priceContainer, init_Price, eventName ]);

            // });
        }

        // Default price hiding function
        function hideDefaultPrice(){
            // Default Price hiding condition
            switch( vpdPublicObject.hideDefaultPrice ){
                case 'no':
                    $(product_wrapper).find(default_price_class).removeClass('hide_default_price');
                    break;
                default:
                    $(product_wrapper).find(default_price_class).addClass('hide_default_price'); 
            }
        }

        // Regenerate variation data
        var quickViewClasses= '.yith-quick-view-overlay,#yith-quick-view-close,.botiga-quick-view-popup-close-button';

        $(document).find( quickViewClasses ).on('click', function(event){
            $('a[class=reset_variations]').click();
        });

        // Botiga Theme quick view overlay clicking event
        $(document).find( '.botiga-quick-view-popup' ).on('click', function(event){
            if (null === event.target.closest('.botiga-quick-view-popup-content-ajax')) {
                $('a[class=reset_variations]').click();
            }
        });
    };

    // Initialize after loading the page or, generate error
    $(document).ready(function(){
        try {
            $(document).trigger('vpd_script_init');
        }
        catch (err) {
            window.console.log('Variation Price Display:', err);
        }
    });

    // Initialize the script on triggering the `vpd_script_init`
    $(document).on('vpd_script_init', function(){
        vpdInit();
    });

    // console.log(vpdPublicObject);


} )( jQuery );