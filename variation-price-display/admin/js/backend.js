(function ($) {

    $(document).ready(function(){

      var $minTR, $maxTR, $customTR, $salePriceTR, $vpdAdminObject;

      // Getting VPD Admin Object
      $vpdAdminObject = vpd_admin_object;

      // Getting <tr> using fields
      $minTR = $( '.from_before_min_price' ).closest('tr');
      $maxTR = $( '.up_to_before_max_price' ).closest('tr');
      $customTR = $( '.custom_price_text' ).closest('tr');
      $salePriceTR = $( '.format_sale_price' ).closest('tr');
      $categories = $( '.categories' ).closest('tr');

      // Callback function to display <tr> based on data
      minMaxConditions( $vpdAdminObject.priceType );
      

      // Expressions indented
      $( '.price_types' ).on('change', function(){
          
          // Getting value on change dropdown data
          var $priceType = $(this).val();

          // Callback function to display <tr> based on data
          minMaxConditions( $priceType );

      });


      // Condition Function for Minimum Maximum <tr>
      function minMaxConditions($priceType){

          // Initially hiding both <tr> for minimum and maximum checkbox
          switch( $priceType ) {

              case 'min':
                  $minTR.show();
                  $maxTR.hide();
                  $customTR.hide();
                  $salePriceTR.show();
                  break;

              case 'max':
                  $minTR.hide();
                  $customTR.hide();
                  $maxTR.show();
                  $salePriceTR.show();
                  break;

              case 'custom':
                  $minTR.hide();
                  $customTR.show();
                  $maxTR.hide();
                  $salePriceTR.show();
                  break;

              case 'list_variations':
                  $minTR.hide();
                  $customTR.hide();
                  $maxTR.hide();
                  $salePriceTR.show();
                  break;

              default:
                  $minTR.hide();
                  $maxTR.hide();
                  $customTR.hide();
                  $salePriceTR.hide();

          }

      }

      // Expressions indented

      if( 'none' === $vpdAdminObject.ExInCondition ){

        $categories.hide();

      }
      else{

        $categories.show();

      }
      
      // Exclude Include Condition
      $( '.exin_condition' ).on('change', function(){
          
          // Getting value on change dropdown data
          var $condition = $(this).val();

          // Callback function to display <tr> based on data
          exinConditions( $condition );

      });

      // Condition Function for Exclude/Include Condition <tr>
      function exinConditions($condition){

          // Initially hiding both <tr> for minimum and maximum checkbox
          switch( $condition ) {

              case 'none':
                  $categories.hide();
                  break;

              default:
                  $categories.show();

          }

      }

      // Initilization of Select 2 JS
      $('.wpx-multiselect').select2();

      // Color Picker
      $('.color-field').wpColorPicker();


    });

}(jQuery));