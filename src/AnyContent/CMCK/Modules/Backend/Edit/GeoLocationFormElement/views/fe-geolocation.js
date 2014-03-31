(function ($) {


    $.fn.cmck_fe_geolocation = function () {


        var init = function () {
            $('.formelement-geolocation-modal-button').click(function () {


                var lat = $($(this).attr('data-input') + '_lat');
                var long = $($(this).attr('data-input') + '_long');

                var options = {lat: lat, long: long};

                console.log (options);

                var onShown = function () {
                    parent.cmck_modal_shown(options);

                };
                parent.cmck_modal($(this).attr('href'), onShown, options);
                return false;
            });
        };

        $(document).on("cmck", function (e, params) {


            switch (params.type) {


                case 'editForm.init':

                    init();
                    break;
            }
        });
        init();

    }

    $(document).ready(function () {
        $(document).cmck_fe_geolocation();
    });

    /* ---------------------------------------- */
})(jQuery);

