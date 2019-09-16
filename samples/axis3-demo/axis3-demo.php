<?php
/**
 * Plugin Name: Axis3 데모 플러그인
 * Description: Axis3 플러그인 구현 예시 플러그인입니다.
 * Version:     1.0.0
 */

use function Shoplic\Axis3Sample\Functions\initStarter;

require_once __DIR__ . '/vendor/autoload.php';

define('AXIS3_DEMO_MAIN', __FILE__);
define('AXIS3_DEMO_VERSION', '1.0.0');

try {
    $starter = initStarter(AXIS3_DEMO_MAIN, AXIS3_DEMO_VERSION);
    $starter->start();
} catch (Exception  $e) {
    wp_die($e->getMessage());
}
