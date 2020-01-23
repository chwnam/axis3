<?php
/**
 * @author    Changwoo Nam
 * @copyright 2019 Changwoo Nam
 * @license   GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: Axis 3 Framework
 * Description: A WordPress must-use (MU) plugin for developing highly customized, modern PHP based websites
 * Version:     0.0.0
 * Author:      Changwoo Nam
 * Author URI:  mailto://changwoo@shoplic.kr
 * Plugin URI:  https://github.com/chwnam/axis3/
 * Text Domain: axis3
 * License:     GPL-2.0+
 */

require_once __DIR__ . '/vendor/autoload.php';

define('AXIS3_VERSION', '0.0.0');
define('AXIS3_MAIN', __FILE__);

if (defined('WP_CLI') && WP_CLI && class_exists('WP_CLI')) {
    try {
        WP_CLI::add_command('axis', \Shoplic\Axis3\Cli\CliHandler::class);
    } catch (Exception $e) {
        echo "WP CLI error: " . $e->getMessage();
    }
}

add_action( 'admin_enqueue_scripts', 'Shoplic\Axis3\Functions\adminEnqueueScripts' );

// TODO: 다양한 값 타입 흡수. 자버의 좌표, 맵
