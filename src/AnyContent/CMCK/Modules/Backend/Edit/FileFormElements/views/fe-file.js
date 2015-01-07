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
/*
                $('.formelement-file-modal-button-view').click(function () {

                    var input = $($(this).attr('data-input')).attr('value');

                    $('#modal_files_file_zoom_title').html(input);
                    $('#modal_files_file_zoom_iframe').attr(input);

                    //var url = 'http://anycontent-g.hahnair.dev/file/53f621c7cf1b3d7632e50f7401bae796/view/Agencies/Profiles/2014/07/7d613544-5303-e411-bd4f-005056ad001b/image-upload-2014-07-08-53bbf9852afdc.jpg';
                    var options = {keyboard: true, remote: url};
                    $('#modal_files_file_zoom').modal(options);

                    return false;
                });*/
                break;

        }

    });


    /* ---------------------------------------- */
})(jQuery);



