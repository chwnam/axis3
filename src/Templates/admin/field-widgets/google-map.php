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
        <label for="<?php echo esc_attr($addr_id); ?>" class="address-field-label">
            <?php esc_html_e('Address', 'axis3'); ?>
        </label>
        <input id="<?php echo esc_attr($addr_id); ?>"
               type="text"
               class="text address-field"
               name="<?php echo esc_attr($addr_name); ?>"
               value="<?php echo esc_attr($addr_value); ?>"
               autocomplete="off">
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

<script type="text/template" id="tmpl-axis3-google-map-info-window">
    <div class="axis3-field-widget axis3-google-map-widget info-window-panel">
        <h3 class="info-window-title"><?php esc_html_e('Selected', 'axis3'); ?></h3>
        <div>
            <p style="margin: 5px 0 10px;"><strong class="info-window-address">{{ data.address }}</strong></p>
            <div class="alignright">
                [<a href="#" id="apply-to-input" role="button" style="font-size: 14px;">
                    <?php esc_html_e('Apply', 'axis3'); ?></a>]
            </div>
            <div class="wp-clearfix"></div>
        </div>
    </div>
</script>
