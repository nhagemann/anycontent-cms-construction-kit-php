(function ($) {

    $(document).on("cmck", function (e, params) {


        switch (params.type) {


            case 'editForm.init':
            case 'sequenceForm.init':
            case 'sequenceForm.refresh':

                $('.formelement-file-modal-button').click(function () {


                    // get the input field, which shall contain latitude and longitude upon selection
                    var input = $($(this).attr('data-input'));

                    var options = {input: input};

                    console.log (input);

                    // when showing the modal, call the function within the just loaded modal and provide pointer to the input field
                    var onShown = function () {
                        parent.cmck_fe_files_modal_shown(options);
                    };

                    // start modal on top level
                    parent.cmck_modal($(this).attr('href'), onShown);

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



