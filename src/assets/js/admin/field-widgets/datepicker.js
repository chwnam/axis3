if (window.hasOwnProperty('jQuery') && 'function' === typeof window.jQuery) {
    (function ($) {
        $.fn.axis3Datepicker = function (opt, addon) {
            var $this = this;

            if (addon) {
                $.timepicker.setDefaults($.timepicker.regional['ko']);
                $this
                    .datetimepicker(opt || {})
                    .on('keypress', function (e) {
                        e.preventDefault();
                    });
            } else {
                $this
                    .datepicker(opt || {})
                    .on('keypress', function (e) {
                        e.preventDefault();
                    });
            }
            $this.siblings('.axis3-datepicker-widget.button').click(function () {
                $this.siblings('.axis3-datepicker-widget.hidden').val('');
                $this.val('');
            });
            return this;
        }
    })(window.jQuery);
}
