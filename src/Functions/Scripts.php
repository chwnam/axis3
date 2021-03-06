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
        'axis3-attach-media',
        $assetUrlBase . '/js/admin/attach-media.js',
        ['jquery', 'media-editor'],
        AXIS3_VERSION,
        true
    );

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

    wp_register_script(
        'axis3-timepicker-addon',
        $assetUrlBase . '/js/jquery-ui-timepicker-addon.min.js',
        ['jquery-ui-datepicker', 'jquery-ui-slider'],
        '1.6.3',
        true
    );

    wp_register_script(
        'axis3-timepicker-addon-i18n',
        $assetUrlBase . '/js/jquery-ui-timepicker-addon-i18n.min.js',
        ['axis3-timepicker-addon'],
        '1.6.3',
        true
    );

    wp_register_script(
        'axis3-google-map',
        $assetUrlBase . '/js/admin/field-widgets/google-map.js',
        ['jquery', 'wp-util', 'axis3-google-map-api'],
        AXIS3_VERSION,
        true
    );

    wp_register_script(
        'axis3-kakao-map',
        $assetUrlBase . '/js/admin/field-widgets/kakao-map.js',
        ['jquery', 'wp-util', 'axis3-kakao-map-api'],
        AXIS3_VERSION,
        true
    );

    wp_register_script(
        'axis3-media-library-selector-widget',
        $assetUrlBase . '/js/admin/field-widgets/media-library-selector.js',
        ['jquery', 'axis3-attach-media'],
        AXIS3_VERSION,
        true
    );

    wp_register_script(
        'axis3-clipboard',
        $assetUrlBase . '/js/clipboard.min.js',
        [],
        '2.0.6'
    );

    wp_register_script(
        'axis3-prism',
        $assetUrlBase . '/js/prism.min.js',
        ['axis3-clipboard'],
        '1.17.1',
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

    wp_register_style(
        'axis3-github-markdown',
        $assetUrlBase . '/css/github-markdown.css',
        [],
        '3.0.1'
    );

    wp_register_style(
        'axis3-admin-github-markdown',
        $assetUrlBase . '/css/admin/admin-github-markdown.css',
        ['axis3-github-markdown'],
        AXIS3_VERSION
    );

    wp_register_style(
        'axis3-prism',
        $assetUrlBase . '/css/prism.css',
        [],
        '1.17.1'
    );

    wp_register_style(
        'axis3-timepicker-addon',
        $assetUrlBase . '/css/jquery-ui-timepicker-addon.min.css',
        ['axis3-jquery-ui'],
        '1.6.3'
    );
}
