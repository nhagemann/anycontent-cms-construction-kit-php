(function ($) {

    $.fn.cmck_fe_file = function () {


        var init = function () {
            $('.formelement-file-modal-button').click(function () {

                parent.cmck_modal($(this).attr('href'));
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
        $(document).cmck_fe_file();

    });

    /* ---------------------------------------- */
})(jQuery);

