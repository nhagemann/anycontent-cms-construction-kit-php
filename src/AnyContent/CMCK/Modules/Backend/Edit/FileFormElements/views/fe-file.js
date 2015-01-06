(function ($) {

    $(document).on("cmck", function (e, params) {


        switch (params.type) {


            case 'editForm.init':
            case 'sequenceForm.init':
            case 'sequenceForm.refresh':

                $('.formelement-file-modal-button').click(function () {

                    // get the input field
                    var input = $($(this).attr('data-input'));

                    parent.cmck_set_var('fe_file_property',input.attr('name'));

                    parent.cmck_modal($(this).attr('href'));

                    return false;
                });
/*
                $('.formelement-file-modal-button-view').click(function () {

                    var input = $($(this).attr('data-input')).attr('value');


                    $('#modal_files_file_zoom_title').html(input);
                    $('#modal_files_file_zoom_iframe').attr(input);

                    var options = {keyboard: true};
                    $('#modal_files_file_zoom').modal(options);

                    return false;
                });*/
                break;

        }

    });


    /* ---------------------------------------- */
})(jQuery);



