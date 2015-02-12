// This file gets included when editing content or config records. In contrast to 'edit.js' this file does NOT get included
// in sequence editing iframe.

function cmck_modal_id(id, url, onShown) {
    id = '#' + id;

    $(id).off('shown.bs.modal');

    $(id).on('shown.bs.modal', function () {

        if (typeof onShown == 'function') {
            onShown();
        }
    });

    $(id).removeData();

    $(id).modal({
        keyboard: true,
        remote: url
    });
}

function cmck_modal(url, onShown) {
    cmck_modal_id('modal_edit', url, onShown);
}


function cmck_modal_id_hide(id) {
    id = '#' + id;

    $(id).modal('hide');
    $(id).removeData();
}


function cmck_modal_hide() {
    cmck_modal_id_hide('modal_edit');

}

function cmck_modal_set_property(name, value) {
    $('#form_edit [name=' + name + ']').val(value);
}

function cmck_set_var(name, value) {
    if (typeof $.cmck != 'object') {
        $.cmck = {};
    }
    $.cmck[name] = value;
}

function cmck_get_var(name, value) {
    return $.cmck[name];
}

function cmck_trigger_change(object) {
    $(object).trigger('change');
    $('iframe').each(function (k, v) {
        if (typeof v.contentWindow.cmck_sequence_trigger_change == 'function') {
            v.contentWindow.cmck_sequence_trigger_change(object);
        }
    });
}

function cmck_message_alert(message) {
    $('#messages').html('<div class="alert alert-warning">' + message + '</div>');
    $(document).scrollTop(0);
    $('#messages div').delay(3000).fadeOut(500);
}

function cmck_message_error(message) {
    $('#messages').html('<div class="alert alert-danger">' + message + '</div>');
    $(document).scrollTop(0);
    $('#messages div').delay(3000).fadeOut(500);
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

    // manual saving of edit form triggers an event - in opposite to internal calls for submitting the form
    $('#form_edit_button_submit').click(function () {
        $.event.trigger('cmck', {type: 'editform.Save'});
        $('#form_edit').submit();
        return false;
    });

    $('#form_edit').submit(function () {

        // Interrupt posting of edit form to check for sequences and allow them to convert their input into
        // a json representation for the containing property

        countdown = parseInt($('#form_edit').attr('data-event-countdown'));


        if (countdown == 0) { // no more sequences to be processed

            counterrors = 0;
            $('.form-group.mandatory').each(function () {

                $(this).removeClass('has-error');
                idFormelement = $(this).attr('data-formelement');
                val = '';
                // Check value of all form fiels having a id starting with the string provided in data-formelement.
                // Usually it will be exactly one form fields, but some form elements split the input into different
                // form fields, e.g. "geolocation".
                $('[id^="'+ idFormelement+'"]').each(function(){
                    val = val + $(this).val().trim();
                    console.log(val);
                });

                if (val == '') {
                    $(this).addClass('has-error');
                    counterrors++;
                }
            });
            if (counterrors > 0) {
                cmck_message_alert('Please fill in all required fields.');
                return false;
            }

            $.blockUI({message: null});

            $.post($('#form_edit').attr('action'), $('#form_edit').serialize()).fail(function (data) {
                cmck_message_error('Failed to save record. Please try again later or contact your administrator.');
                $.unblockUI();
            }).done(function (response) {

                if (response.success != undefined) {

                    if (response.success == true) {
                        location.href = response.redirect;
                        return false;
                    } else {
                        if (response.message != undefined) {
                            cmck_message_alert(response.message);
                            if (response.properties != undefined) {
                                for (i = 0; i < response.properties.length; i++) {
                                    property = response.properties[i];
                                    input = $('input[name="' + property + '"]');
                                    id = input.attr('id');
                                    $('div.form-group[data-formelement="' + id + '"]').addClass('has-error');
                                }
                            }
                            $.unblockUI();
                            return false;
                        }
                    }
                }
                cmck_message_error('Failed to save record. Please try again later or contact your administrator.');


                $.unblockUI();
            });


            return false;

        }
        return false;
    });


    $('#form_edit_button_transfer').click(function () {

        cmck_modal($(this).attr('href'));
        return false;
    });


    // capture CTRL+S, CMD+S
    $(document).keydown(function (e) {
        if ((e.which == '115' || e.which == '83' ) && (e.ctrlKey || e.metaKey)) {
            e.preventDefault();
            $('#form_edit_button_submit').click();
            return false;
        }
        return true;
    });

    // inform form elements about loading of the editing form
    $.event.trigger('cmck', {type: 'editForm.init'});
});



