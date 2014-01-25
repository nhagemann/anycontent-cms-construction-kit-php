$(document).ready(function () {


    $('div.formelement-sourcecode textarea').each(function () {

        var mode = 'htmlmixed';
        var myCodeMirror = CodeMirror.fromTextArea(this, {lineNumbers: true, mode: mode});

        //id = $(this).attr('id');
        //rows = $(this).attr('rows');
        //h = 90 + rows * 18;
        //tinymce.init({selector: '#' + id, height: h});


    });


});


