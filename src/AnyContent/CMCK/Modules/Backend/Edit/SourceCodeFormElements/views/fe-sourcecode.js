$(document).on("cmck", function (e, params) {

    switch (params.type) {
        case 'editForm.init':
        //TODO: Find out, why the code mirror textarea replacement doesn't work within sequences (i.e. bootstrap accordions?)
        //case 'sequenceForm.init':
        //case 'sequenceForm.refresh':
            $('.textarea-codemirror').each(function () {

                var options = {lineNumbers: true, lineWrapping: true};

                var mode = ($(this).attr('data-mode'));
                if (mode) {
                    options.mode = mode;
                }
                var moreoptions = $(this).attr('data-options');

                if (moreoptions) {
                    try {
                        moreoptions = JSON.parse(moreoptions);
                        $().extend(options, moreoptions);
                        console.log(moreoptions);
                    } catch (e) {

                    }
                }


                var myCodeMirror = CodeMirror.fromTextArea(this, options);

                width = $(this).width();
                rows = $(this).attr('rows');
                height = 5 + rows * 14;
                myCodeMirror.setSize(width, height);
                myCodeMirror.refresh();
            });
            break;
    }
    ;
});

$(document).ready(function () {


    CodeMirror.defineMode("cmdl", function () {

        var TOKEN_NAMES = {
            '#': 'comment',
            '[': 'header',
            ']': 'header',
            '=': 'string',
            '?': 'meta',
            '@': 'def',
            '"': 'variable-3 em'
        };

        return {

            startState: function () {
                return {};
            },

            token: function (stream, state) {
                var tw_pos = stream.string.search(/[\t ]+?$/);
                //console.log (tw_pos);
                if (!stream.sol() || tw_pos === 0) {
                    //console.log("NEXT"+stream.peek());
                    token_name = 'def'; // Never used ???

                    if (stream.peek('=')) {
                        token_name = 'operator';
                        stream.next();
                        return token_name;
                        //return 'astring';
                    }

                    stream.skipToEnd();
                    //var error = ("error " + (
                    //    TOKEN_NAMES[stream.string.charAt(0)] || '')).replace(/ $/, '');
                    //console.log (error);
                    return token_name;
                }


                var token_name = TOKEN_NAMES[stream.peek()];

                if (token_name == undefined) {
                    token_name = 'variable-2';
                }

                if (stream.skipTo('=')) {
                    return token_name;
                }
                //console.log(stream.peek());
                //stream.skipToEnd();
                //return 'variable-2';

                if (tw_pos === -1) {
                    stream.skipToEnd();

                } else {
                    stream.pos = tw_pos;
                }

                return token_name;
            }
        };
    });

    CodeMirror.defineMIME("text/x-cmdl", "cmdl");


});


