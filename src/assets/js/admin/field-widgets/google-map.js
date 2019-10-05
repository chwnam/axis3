/* global jQuery */
/* global google */
/* global axis3GoogleMap */
(function ($) {
    $.fn.axis3GoogleMap = function () {
        var $this = this,
            address,
            infoWindow,
            geoCoder,
            map,
            marker,
            position,
            template;

        if (this.length) {
            geoCoder = new google.maps.Geocoder();
            infoWindow = new google.maps.InfoWindow();
            map = new google.maps.Map(this[0], this.data('mapOpts'));
            marker = new google.maps.Marker({
                position: this.data('mapOpts').center,
                map: map
            });
            template = wp.template('axis3-google-map-info-window');

            map.addListener('click', function (e) {
                position = e.latLng;
                geoCoder.geocode({location: position}, function (results, status) {
                    if ('OK' === status && results[0]) {
                        address = results[0].formatted_address;
                        map.panTo(position);
                        marker.setPosition(position);
                        infoWindow.setContent(template({address: address}));
                        infoWindow.open(map, marker);
                    }
                });
            });

            this.on('click', '#apply-to-input', function (e) {
                var addr = $('input[name="' + $this.data('addrName') + '"]'),
                    lat = $('input[name="' + $this.data('latName') + '"]'),
                    lng = $('input[name="' + $this.data('lngName') + '"]');
                e.preventDefault();
                if (addr.val().trim().length && !confirm(axis3GoogleMap.textOverwrite)) {
                    return;
                }
                addr.val(address);
                lat.val(position.lat());
                lng.val(position.lng());
                infoWindow.close();
            });
        }
    };
})(jQuery);
