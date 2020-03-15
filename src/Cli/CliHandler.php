<?php

namespace Shoplic\Axis3\Cli;

use Shoplic\Axis3\Starters\ScanCache;
use WP_Cli;

/**
 * Axis 3 CLI 핸들러
 *
 * @package Shoplic\Axis3\Cli
 * @since   1.0.0
 */
class CliHandler
{
    public function __construct()
    {
    }

    /**
     * init_plugin 명령. 아직 테스트용입니다.
     *
     * @param array $args Positional arguments
     * @param array $kwargs Keyword arguments
     */
    public function init_plugin($args, $kwargs)
    {
        print_r($args);
        print_r($kwargs);
        WP_CLI::success('init_plugin() invoked.');
    }
}
