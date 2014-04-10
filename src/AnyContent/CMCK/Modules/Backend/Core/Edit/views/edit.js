$(document).on("cmck", function (e, params) {

    console.log('Edit.js: ' + params.type);

    switch (params.type) {
        case 'editForm.init':
        case 'sequenceForm.init':
        case 'sequenceForm.refresh':
            $('.formelement-popover').popover({html: true});
            $('.formelement-tooltip').popover({html: true});
            break;
    }
    ;
});


$(document).ready(function () {

});