$(document).on("cmck", function (e, params) {

    switch (params.type) {
        case 'editForm.init':
        case 'sequenceForm.init':
        case 'sequenceForm.refresh':
            $('div.formelement-richtext textarea').each(function () {

                id = $(this).attr('id');
                rows = $(this).attr('rows');
                h = 90 + rows * 18;
                tinymce.init({selector: '#' + id, height: h});
            });
            break;
    }
    ;
});
