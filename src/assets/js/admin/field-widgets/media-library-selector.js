if (window.hasOwnProperty('jQuery') && 'function' === typeof window.jQuery) {
    (function ($) {
        $.fn.mediaLibrarySelector = function (opt) {
            var text, button, preview;

            opt = $.extend({
                textButton: '',
                textSelectMedia: 'Select Media',
                textTitle: 'Select or upload media',
                textPreview: 'Preview',
                textPreviewChooseImage: 'Choose an image',
                saveField: 'url'
            }, opt);

            text = $('#' + this.attr('id') + '-text', this);
            button = $('#' + this.attr('id') + '-button', this);
            preview = $('#' + this.attr('id') + '-preview', this);

            button.axis3AttachMedia({
                textButton: opt.textButton,
                textTitle: opt.textTitle,
                selectCallback: function (selection) {
                    var item = selection.length ? selection[0] : {};
                    if (item.url.length && item.id) {
                        text.val(item.url).data('id', item.id).trigger('change');
                    }
                }
            });

            text.on('change', function () {
                if (text.val().length) {
                    preview.attr('href', text.val()).text(opt.textPreview);
                } else {
                    preview.removeAttr('href').text(opt.textPreviewChooseImage)
                }
            }).trigger('change').closest('form').on('submit', function () {
                if ('id' === opt.saveField) {
                    $('<input>', {
                        type: 'hidden',
                        name: text.attr('name'),
                        value: text.data('id')
                    }).appendTo(this);
                    text.removeAttr('name');
                }
            });

            return this;
        };
    })(window.jQuery);
}
