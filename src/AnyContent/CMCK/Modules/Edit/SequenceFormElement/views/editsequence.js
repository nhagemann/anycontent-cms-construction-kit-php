(function ($) {

    // This JavaScript is included on a page containing just one accordion/sortable for editing a sequence. This page
    // is included as an iframe within the parent page. The height of the iframe gets adjusted automatically.


    $.fn.cmck_editsequence = function () {

        var firstRun = true;

        var calcHeight = function () {
            c = $('div.sequence-item').length;
            init = 200 + c * 50;

            current = 150 + $('.sequence-accordion').height();
            h = Math.max(current, init)

            iframe = '#form_edit_sequence_' + $('#form_sequence').attr('data-property') + '_iframe';

            if (firstRun) {
                $(iframe, window.parent.document).height(h);
                firstRun = false;

            }
            else {
                $(iframe, window.parent.document).animate({height: h + 'px'}, 500);
            }


        };


        $(document).on("cmck", function (e, params) {

            switch (params.type) {


                case 'editForm.init':

                    if (params.refresh) {

                        $('.sequence-accordion').accordion('destroy');
                        $('.sequence-add-item li a').off('click');
                        $('.sequence-remove-item').off('click');
                        $(".sequence-accordion").sortable("refresh"); //call widget-function destroy

                    }

                    $(".sequence-accordion").accordion({
                        header: "h3",
                        collapsible: true,
                        heightStyle: "content",
                        active: parseInt($('#form_sequence').attr('data-active-item')),
                        activate: function () {
                            calcHeight();
                        },
                        animated: 'fastslide'
                    });


                    if (!params.refresh) {
                        $(".sequence-accordion").sortable({
                            axis: "y",
                            handle: "h3",
                            stop: function (event, ui) {
                                // IE doesn't register the blur when sorting
                                // so trigger focusout handlers to remove .ui-state-focus
                                ui.item.children("h3").triggerHandler("focusout");
                            }});
                    }


                    $(".sequence-add-item li a").click(function () {
                        insert = $(this).attr('data-insert')
                        item = $(this).closest('ul').attr('data-item');

                        $.event.trigger('cmck', {type: 'sequenceForm.add', insert: insert, item: item});
                    });

                    $(".sequence-remove-item").click(function () {

                        item = $(this).attr('data-item');

                        $.event.trigger('cmck', {type: 'sequenceForm.remove', item: item});
                    });


                    calcHeight();


                    break;

                case 'sequenceForm.add':


                    count = parseInt($('#form_sequence').attr('data-count')) + 1;

                    $('#form_sequence').attr('data-count', count);
                    $.get($('#form_sequence').attr('data-action-add') + '?insert=' + params.insert + '&count=' + count, function (data) {


                        item = params.item;
                        if (parseInt(item) > 0) {
                            $('#form_sequence_item_' + item).after(data);
                        }
                        else {
                            $('.sequence-accordion').append(data);
                        }

                        $.event.trigger('cmck', {type: 'editForm.init', refresh: true});

                    });


                    break;

                case 'sequenceForm.remove':
                    item = parseInt(params.item);
                    $('#form_sequence_item_' + item).remove();

                    $.event.trigger('cmck', {type: 'editForm.init', refresh: true});

                    break;
            }


        });


        $.event.trigger('cmck', {type: 'editForm.init'});
    };


    $(document).ready(function () {
        $(document).cmck_editsequence();
    });

    /* ---------------------------------------- */
})(jQuery);



