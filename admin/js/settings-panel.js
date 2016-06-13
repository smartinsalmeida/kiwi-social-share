(function($) {

    'use strict';

    /* ==========================================================================
     When document is ready, do
     ========================================================================== */
    $(document).ready(function() {

        if (typeof $.fn.sortable !== 'undefined') {

            $('.kiwi-form-wrapper-left').sortable({
                items: '.kiwi-field-wrapper.checkbox-sortable',
                handle: '.kiwi-sortable-helper',
                containment: 'parent',
                cursor: 'move',
                placeholder: 'checkbox-sortable-placeholder',
                update: function() {
                    if( $('.kiwi-form-wrapper-left .checkbox-sortable').length && $('#general_settings_order').length ){

                        var order_update = '';

                        $(".checkbox-sortable .switch-input").each(function(){
                            var id = $(this).attr('id');
                            if(order_update == '' ) {
                                order_update = id;
                            } else {
                                order_update = order_update + ',' + id;
                            }
                        });
                        // rewrite the value of the order input field
                        $('.kiwi-form-wrapper-left #general_settings_order').val(order_update);
                    }
                }
            });
        } // end sortable

        // helper function for the radio-img field
        function kiwi_set_radion_img_button() {

            $('.kiwi-field-helper.background-image').on( 'click', function(){
                var inputID = $(this).data('click-to');

                // now go ahead and move the click to the radio button
                $('input#' +inputID ).click();
            });
        }


        //=====================
        // Run functions here
        //=====================

        kiwi_set_radion_img_button();


    });
})(window.jQuery);