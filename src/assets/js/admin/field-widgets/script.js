if (window.hasOwnProperty('jQuery') && typeof window.jQuery === 'function') {
    (function ($) {
        if ('function' === typeof $.fn.tooltip) {
            $(document).tooltip({
                items: 'span.axis3-widget-tooltip',
                position: {
                    'my': 'left top+5',
                    'at': 'left bottom'
                },
                content: function () {
                    return this.dataset.tooltip;
                }
            });
        }

        $('.axis3-field-widget:required').filter(function () {
            return this.title.length > 0;
        }).on('invalid', function () {
            this.setCustomValidity('');
            if (!this.validity.valid) {
                this.setCustomValidity(this.title);
            }
        }).on('input', function () {
            this.setCustomValidity('');
        });
    })(window.jQuery);
}
