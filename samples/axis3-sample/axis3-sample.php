<?php
/**
 * Plugin Name: Axis3 샘플 플러그인
 * Description: Axis3 플러그인 구현 샘플을 보여 주는 플러그인입니다.
 * Version:     1.0.0
 */

use function Shoplic\Axis3Sample\Functions\getStarter;

require_once __DIR__ . '/vendor/autoload.php';

define('AXIS3_SAMPLE_MAIN', __FILE__);
define('AXIS3_SAMPLE_VERSION', '1.0.0');

try {
    $starter = getStarter(AXIS3_SAMPLE_MAIN, AXIS3_SAMPLE_VERSION);
    $starter->start();
} catch (Exception  $e) {
    wp_die($e->getMessage());
}
