$(document).ready(function () {

    $('#form_edit_button_save_options a').click(function () {
        $('#form_edit_button_save').removeClass('open');
        $('#form_edit_button_save input:first').attr('value', $(this).text());
        $('#form_edit_button_save_operation').attr('value', $(this).attr('data-operation'));
        return false;
    });


    $('#form_edit_button_submit').click(function () {


        $.event.trigger('cmck', {type: 'editform.Save'});
        countdown = parseInt($('#form_edit').attr('data-event-countdown'));
        if (countdown==0)
        {
            return true;
        }
        return false;
    });


    $(document).on("cmck", function (e, params) {


        switch (params.type) {
            case 'editform.setProperty':
                $('#form_edit [name=' + params.property + ']').val(params.value);

                if (params.save == true) {
                    if (parseInt($('#form_edit').attr('data-event-countdown'))==0)
                    {
                        $('#form_edit').submit();
                    }
                }
                break;
        }

    });

});