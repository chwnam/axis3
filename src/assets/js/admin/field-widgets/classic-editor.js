if (window.hasOwnProperty('jQuery') && 'function' === typeof window.jQuery) {
    (function ($) {
        var template;

        window.axis3ClassicEditor = function (obj) {
            if (_ && tinymce && tinyMCEPreInit) {
                var init, qtInit;

                obj = _.extend({
                    dummyId: '',
                    editorId: '',
                    editorName: '',
                    content: '',
                    target: '',
                }, obj);
                init = _.clone(tinyMCEPreInit.mceInit[obj.dummyId]);
                qtInit = _.clone(tinyMCEPreInit.qtInit[obj.dummyId]);

                $(obj.target).html(template({
                    editorId: obj.editorId,
                    editorName: obj.editorName,
                    content: obj.content
                }));

                init.selector = '#' + obj.editorId;
                qtInit.id = obj.editorId;

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
