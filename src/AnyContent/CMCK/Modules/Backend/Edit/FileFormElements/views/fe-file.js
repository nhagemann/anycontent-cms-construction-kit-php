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

                    // when showing the modal, call the function within the just loaded modal and provide pointer to the input field
                    var onShown = function () {
                        parent.cmck_fe_files_modal_shown(options);
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



