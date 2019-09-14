<?php

namespace Shoplic\Axis3\Functions;

/**
 * @callback
 * @action      admin_enqueue_scripts
 */
function adminEnqueueScripts()
{
    $assetUrlBase = plugins_url('src/assets', AXIS3_MAIN);

    wp_register_script(
        'axis3-field-widgets',
        $assetUrlBase . '/js/admin/field-widgets/script.js',
        ['jquery'],
        AXIS3_VERSION,
        true
    );

    wp_register_script(
        'axis3-datepicker-widget',
        $assetUrlBase . '/js/admin/field-widgets/datepicker.js',
        ['jquery', 'jquery-ui-datepicker'],
        AXIS3_VERSION,
        true
    );

    wp_register_style(
        'axis3-jquery-ui',
        filterScriptUrl($assetUrlBase . '/css/jquery-ui.min.css'),
        [],
        AXIS3_VERSION
    );

    wp_register_style(
        'axis3-field-widgets',
        $assetUrlBase . '/css/admin/field-widgets/style.css',
        [],
        AXIS3_VERSION
    );
}
