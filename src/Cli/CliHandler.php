<?php

namespace Shoplic\Axis3\Cli;

use Shoplic\Axis3\Starters\StarterPool;
use WP_Cli;
use WP_CLI\ExitException;

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
     * @param array $args   Positional arguments
     * @param array $kwargs Keyword arguments
     */
    public function init_plugin($args, $kwargs)
    {
        print_r($args);
        print_r($kwargs);
        WP_CLI::success('init_plugin() invoked.');
    }

    /**
     * 플러그인에서 정의한 숏코드 목록을 알려줍니다.
     *
     * ## OPTIONS
     * <slug>
     * : 플러그인을 담당하는 스타터의 슬러그.
     *
     * <output>
     * : 조사된 내용을 출력할 마크다운 파일 경로.
     *
     * @param array $args 인자. 0번으로 반드시 플러그인의 슬러그를 입력.
     *
     * @throws ExitException 정확하지 않은 슬러그 입력시 예외 처리.
     */
    public function inspect_shortcode($args)
    {
        $starter = StarterPool::getInstance()->getStarter($args[0]);
        if (!$starter) {
            WP_CLI::error("스타터 '{$args[0]}'는 존재하지 않습니다.");
        }

        $inspector = new ShortcodeInspector($starter);
        $inspector->renderMarkdown($inspector->inspect(), $args[1]);

        WP_CLI::success('숏코드 분석 파일을 생성했습니다.');
    }
}
