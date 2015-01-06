$('.file-select-item').click(function () {


    var value = $(this).attr('data-src');
    parent.cmck_modal_set_property(parent.cmck_get_var('fe_file_property'),value);
    parent.cmck_modal_hide();


});