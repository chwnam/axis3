/* global jQuery */
/* global kakao */
/* global axis3KakaoMap */
(function ($) {
    $.fn.axis3KakaoMap = function () {
        var $this = this,
            address,
            infoWindow,
            geoCoder,
            map,
            marker,
            mapOpts,
            position,
            template;

        if (this.length) {
            mapOpts = this.data('mapOpts');
            geoCoder = new kakao.maps.services.Geocoder();
            infoWindow = new kakao.maps.InfoWindow({zindex: 1});
            map = new kakao.maps.Map(this[0], {
                center: new kakao.maps.LatLng(parseFloat(mapOpts.center.lat), parseFloat(mapOpts.center.lng)),
                level: mapOpts.level
            });
            marker = new kakao.maps.Marker({
                position: map.getCenter()
            });

            map.addControl(new kakao.maps.ZoomControl(), kakao.maps.ControlPosition.RIGHT);
            marker.setMap(map);

            template = wp.template('axis3-kakao-map-info-window');

            kakao.maps.event.addListener(map, 'click', function (e) {
                position = e.latLng;
                geoCoder.coord2Address(position.getLng(), position.getLat(), function (results, status) {
                    var includePostcode = $('#kakao-map-include-postcode').is(':checked'),
                        roadAddress = !!results[0].road_address;
                    if (status === kakao.maps.services.Status.OK && results[0]) {
                        map.panTo(position);
                        marker.setPosition(position);
                        infoWindow.setContent(template({
                            address: results[0].address.address_name,
                            roadAddress: roadAddress ? results[0].road_address.address_name : null,
                            postcode: roadAddress && includePostcode ? results[0].road_address.zone_no : ''
                        }));
                        infoWindow.open(map, marker);
                    }
                });
            });

            this.on('click', '#axis3-kakao-map-apply', function (e) {
                var addr = $('input[name="' + $this.data('addrName') + '"]'),
                    lat = $('input[name="' + $this.data('latName') + '"]'),
                    lng = $('input[name="' + $this.data('lngName') + '"]'),
                    selectedAddr = $('[name="kakao_map_address"]:checked'),
                    includePostcode = $('#kakao-map-include-postcode').is(':checked');
                e.preventDefault();
                if (addr.val().trim().length && !confirm(axis3KakaoMap.textOverwrite)) {
                    return;
                }
                if (includePostcode) {
                    addr.val(selectedAddr.data('postcode') + ' ' + selectedAddr.val());
                } else {
                    addr.val(selectedAddr.val());
                }
                lat.val(position.getLat());
                lng.val(position.getLng());
                infoWindow.close();
            });
        }
    };
})(jQuery);
