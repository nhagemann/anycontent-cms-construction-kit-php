(function ($) {


    $(document).on("cmck", function (e, params) {


        switch (params.type) {


            case 'editForm.init':
            case 'sequenceForm.init':
            case 'sequenceForm.refresh':

                $('.formelement-geolocation-modal-button').click(function () {


                    // get the input field, which shall contain latitude and longitude upon selection
                    var lat = $($(this).attr('data-input') + '_lat');
                    var long = $($(this).attr('data-input') + '_long');

                    var options = {lat: lat, long: long};


                    // when showing the modal, call the function within the just loaded modal and provide pointer to the input fields
                    var onShown = function () {
                        parent.cmck_fe_geolocation_modal_shown(options);
                    };

                    // start modal on top level
                    parent.cmck_modal($(this).attr('href'), onShown);
                    return false;
                });
                break;
        }
    });


    /* ---------------------------------------- */
})(jQuery);

