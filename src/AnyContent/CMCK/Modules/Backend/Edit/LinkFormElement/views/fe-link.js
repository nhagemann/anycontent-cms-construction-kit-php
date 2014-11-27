(function ($) {

    /* ---------------------------------------- */

    $.fn.cmck_fe_link = function () {


        // http://stackoverflow.com/questions/1303872/trying-to-validate-url-using-javascript

        var validateUrl = function (val) {
            var urlregex = new RegExp(
                "^(http|https|ftp)\://([a-zA-Z0-9\.\-]+(\:[a-zA-Z0-9\.&amp;%\$\-]+)*@)*((25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])|([a-zA-Z0-9\-]+\.)*[a-zA-Z0-9\-]+\.(com|edu|gov|int|mil|net|org|biz|arpa|info|name|pro|aero|coop|museum|[a-zA-Z]{2}))(\:[0-9]+)*(/($|[a-zA-Z0-9\.\,\?\'\\\+&amp;%\$#\=~_\-]+))*$");
            return urlregex.test(val);
        }

        var checkInput = function (e) {

            $(this).removeClass('alert-success');
            $(this).removeClass('alert-danger');

            url = $(this).val();

            if (url != '') {
                if (validateUrl(url)) {

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
                }
                $(this).addClass('alert-danger');
            }
        }

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
    }

    $(document).cmck_fe_link();

    /* ---------------------------------------- */

})(jQuery);

