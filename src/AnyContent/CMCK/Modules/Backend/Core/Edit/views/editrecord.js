// This file gets included when editing content or config records. In contrast to 'edit.js' this file does NOT get included
// in sequence editing iframe.


function cmck_modal(url, onShown) {

    $('#modal_edit').removeData();
    var target = $('#modal_edit');
    $(target).modal({
        keyboard: true,
        remote: url
    }).on('shown.bs.modal', onShown).on('hide.bs.modal', function () {
        // make sure the modal content is loaded everytime and all event listeners are deleted
        $('#modal_edit').removeData();
        //$('#modal_edit',parent.window.document).removeData();
        $(target).unbind();
    });

};


$(document).on("cmck", function (e, params) {


    switch (params.type) {
        case 'editform.setProperty': // Used from sequences upon storing.


            $('#form_edit [name=' + params.property + ']').val(params.value);

            if (params.save == true) {
                if (parseInt($('#form_edit').attr('data-event-countdown')) == 0) {
                    $('#form_edit').submit();
                }
            }
            break;
    }

});



$(document).ready(function () {

    $('#form_edit_button_save_options a').click(function () {
        $('#form_edit_button_save').removeClass('open');
        $('#form_edit_button_save input:first').attr('value', $(this).text());
        $('#form_edit_button_save_operation').attr('value', $(this).attr('data-operation'));
        return false;
    });


    // Interrupt posting of edit form to check for sequences and allow them to convert their input into
    // a json representation for the containing property
    $('#form_edit_button_submit').click(function () {


        $.event.trigger('cmck', {type: 'editform.Save'});
        countdown = parseInt($('#form_edit').attr('data-event-countdown'));

        if (countdown == 0) {
            return true;
        }
        return false;
    });


    $('#form_edit_button_transfer').click(function () {

        cmck_modal($(this).attr('href'));
        return false;
    });

    // inform form elements about loading of the editing form
    $.event.trigger('cmck', {type: 'editForm.init'});
});



