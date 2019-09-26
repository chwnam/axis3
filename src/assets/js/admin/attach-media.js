if (window.hasOwnProperty('jQuery') && 'function' === typeof window.jQuery) {
    (function ($) {
        $.fn.axis3AttachMedia = function (opt) {
            var frame;

            opt = $.extend({
                multiple: false,
                library: {}, // put additional parameters to ajax calls.
                selectCallback: function (selection) {
                    console.log(selection);
                },
                textButton: 'Use this media',
                textTitle: 'Select or Upload Media'
            }, opt);

            this.on('click', function (e) {
                e.preventDefault();

                if (frame) {
                    frame.open();
                } else {
                    frame = wp.media({
                        title: opt.textTitle,
                        button: {
                            text: opt.textButton
                        },
                        multiple: opt.multiple,
                        library: opt.library
                    });
                    frame.on('select', function () {
                        opt.selectCallback($.map(frame.state().get('selection').models, function (obj) {
                            return obj.toJSON();
                        }));
                    }).open();
                }
            });

            return this;
        }
    })(window.jQuery);
}