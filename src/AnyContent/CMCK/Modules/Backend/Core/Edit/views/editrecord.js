// This file gets included when editing content or config records. In contrast to 'edit.js' this file does NOT get included
// in sequence editing iframe.

function cmck_modal_id(id, url, onShown)
{
    id = '#' + id;

    $(id).off('shown.bs.modal');

    $(id).on('shown.bs.modal', function () {

        if (typeof onShown == 'function')
        {
            onShown();
        }
    });

    $(id).removeData();

    $(id).modal({
        keyboard: true,
        remote  : url
    });
}

function cmck_modal(url, onShown ) {
    cmck_modal_id('modal_edit',url, onShown);
}


function cmck_modal_id_hide(id)
{
    id = '#' + id;

    $(id).modal('hide');
    $(id).removeData();
}


function cmck_modal_hide()
{
    cmck_modal_id_hide('modal_edit');

}

function cmck_modal_set_property(name,value)
{
    $('#form_edit [name=' + name + ']').val(value);
}

function cmck_set_var(name,value)
{
    if  (typeof $.cmck != 'object') {
        $.cmck = {};
    }
    $.cmck[name] = value;
}

function cmck_get_var(name,value)
{
    return $.cmck[name];
}

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



