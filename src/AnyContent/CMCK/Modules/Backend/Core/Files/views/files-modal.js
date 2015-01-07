$('.file-select-item').click(function () {

    var value = $(this).attr('data-src');
    input = parent.cmck_get_var('fe_file_property');
    $(input).val(value);
    parent.cmck_modal_hide();

});