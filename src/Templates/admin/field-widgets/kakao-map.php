<?php
/**
 * @var string $addr_id
 * @var string $addr_name
 * @var string $addr_value
 * @var string $lat_name
 * @var string $lng_name
 *
 * @var array  $map_attrs
 * @var array  $map_opts
 * @var array  $wrap_attrs
 */

use function Shoplic\Axis3\Functions\formatAttr;

?>
<div <?php echo formatAttr($wrap_attrs); ?>>
    <div <?php echo formatAttr($map_attrs); ?>
            data-map-opts="<?php echo esc_attr(wp_json_encode($map_opts ?? '')); ?>"
            data-addr-name="<?php echo esc_attr($addr_name); ?>"
            data-lat-name="<?php echo esc_attr($lat_name); ?>"
            data-lng-name="<?php echo esc_attr($lng_name); ?>">
    </div>
    <div>
        <div>
            <label for="<?php echo esc_attr($addr_id); ?>" class="address-field-label">
                <?php esc_html_e('Address', 'axis3'); ?>
            </label>
            <input id="<?php echo esc_attr($addr_id); ?>"
                   type="text"
                   class="text address-field"
                   name="<?php echo esc_attr($addr_name); ?>"
                   value="<?php echo esc_attr($addr_value); ?>"
                   autocomplete="off">
        </div>
        <div>
            <input id="kakao-map-include-postcode" type="checkbox">
            <label for="kakao-map-include-postcode">
                <?php esc_html_e('Include zip code when road address is chosen.', 'axis3'); ?>
            </label>
        </div>
        <input type="hidden"
               name="<?php echo esc_attr($lat_name); ?>"
               value="<?php echo esc_attr($map_opts['center']['lat']); ?>"
               autocomplete="off">
        <input type="hidden"
               name="<?php echo esc_attr($lng_name); ?>"
               value="<?php echo esc_attr($map_opts['center']['lng']); ?>"
               autocomplete="off">
    </div>
</div>
