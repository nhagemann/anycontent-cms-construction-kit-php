$(document).ready(function () {


    $('div.formelement-richtext textarea').each(function () {

        id = $(this).attr('id');
        rows = $(this).attr('rows');
        h = 90 + rows * 18;
        tinymce.init({selector: '#' + id, height: h});
    });

});
