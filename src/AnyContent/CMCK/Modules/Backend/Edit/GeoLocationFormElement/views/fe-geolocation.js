(function ($) {


    $.fn.cmck_fe_geolocation = function () {


        var init = function () {
            $('.formelement-geolocation-modal-button').click(function () {


                var lat = $($(this).attr('data-input')+'_lat');
                var long = $($(this).attr('data-input')+'_long');

                var options = {lat:lat,long:long,name:name};


                parent.cmck_modal($(this).attr('href'),'modal.shown.geolocation',options);

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

