(function ($) {

    /* ---------------------------------------- */

    $.fn.cmck_fe_link = function () {

        var checkInput = function (e) {

            $(this).removeClass('alert-success');
            $(this).removeClass('alert-danger');

            url = $(this).val();

            if (url != '') {


                var formelement = $(this);

                $.ajax({

                    url: '/edit/check/link/' + url,
                    success: function (result) {

                        if (result) {
                            formelement.addClass('alert-success');
                            formelement.removeClass('alert-danger');
                        }
                        else {
                            formelement.addClass('alert-danger');
                            formelement.removeClass('alert-success');
                        }
                    }
                });
                return;

                $(this).addClass('alert-danger');
            }
        };

        /* ---------------------------------------- */

        $(document).on("cmck", function (e, params) {


            switch (params.type) {
                case 'editForm.init':
                case 'sequenceForm.init':
                case 'sequenceForm.refresh':
                    $('div.formelement-link input').each(function () {

                        val = $(this).val();
                        $(this).on('focus', checkInput);
                        $(this).on('blur', checkInput);

                    });
                    break;
            }

        });
    };


    $(document).cmck_fe_link();

    /* ---------------------------------------- */

})(jQuery);

