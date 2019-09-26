if (window.hasOwnProperty('jQuery') && 'function' === typeof window.jQuery) {
    (function ($) {
        var template;

        window.axis3ClassicEditor = function (target, dummyId, editorId, content) {
            if (_ && tinymce && tinyMCEPreInit) {
                var init = _.clone(tinyMCEPreInit.mceInit[dummyId]),
                    qtInit = _.clone(tinyMCEPreInit.qtInit[dummyId]);
                console.log($(target));
                $(target).html(template({
                    editorId: editorId,
                    content: content
                }));

                init.selector = '#' + editorId;
                qtInit.id = editorId;

                tinymce.init(init);
                if (typeof quicktags !== 'undefined') {
                    quicktags(qtInit);
                }
            }
        };

        if (window.hasOwnProperty('wp') && 'function' === typeof window.wp.template) {
            template = wp.template('axis3-classic-editor-widget');
        }

    })(window.jQuery);
}
