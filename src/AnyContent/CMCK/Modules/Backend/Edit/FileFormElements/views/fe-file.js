(function ($) {

    $(document).on("cmck", function (e, params) {


        switch (params.type) {


            case 'editForm.init':
            case 'sequenceForm.init':
            case 'sequenceForm.refresh':

                $('.formelement-file-modal-button').click(function () {

                    // get the input field
                    var input = $($(this).attr('data-input'));

                    parent.cmck_set_var('fe_file_property',input);

                    parent.cmck_modal($(this).attr('href'));

                    return false;
                });

                $('.formelement-file-modal-button-view').click(function () {

                    var id = $(this).attr('data-input');
                    var value = $(id).val();

                    $(parent.document).find('#modal_files_file_zoom_title').html(value);
                    $(parent.document).find('#modal_files_file_zoom_iframe').attr('src',$(this).attr('href')+value);

                    parent.cmck_modal_id('modal_files_file_zoom');

                    return false;
                });

                $('.formelement-file-modal-button-download').click(function () {

                    var id = $(this).attr('data-input');
                    var value = $(id).val();
                    value = $(this).attr('href')+value;
                    window.location.href = value;

                    return false;
                });
                break;

        }

    });


    /* ---------------------------------------- */
})(jQuery);



