<?php

namespace Shoplic\Axis3\Starters\ClassFinders;

use RegexIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use SplFileInfo;

/**
 * Class AutoDiscoverClassFinder
 *
 * 지정된 경로를 대상으로 콤포넌트를 자동 검색하는 클래스 검색자.
 *
 * @package Shoplic\Axis3\Starters\ClassFinders
 * @since   1.0.0
 */
class AutoDiscoverClassFinder extends BaseClassFinder
{
    const CONTEXT_RULE_HEAD = 'head';
    const CONTEXT_RULE_TAIL = 'tail';

    /** @var string 검색의 대상 경로 */
    private $rootPath;

    /** @var string 검색 경로의 기본 네임스페이스 */
    private $rootNamespace;

    /**
     * @var string|callable 콘텍스트 규칙. 문자열, 혹은 callback.
     *                      PSR-4 에서는 네임스페이스의 중간 부분의 구조가 디렉토리 구조를 닮는다.
     *                      디렉토리의 깊이가 깊어질 때 어떤 부분을 콘텍스트로 받아들일지를 지정한다.
     *                      후보는 아래와 같다.
     *
     *                      - head:     기본. 발견되는 디렉토리의 가장 첫부분을 콘택스트로 가져간다.
     *                      - tail:     발견되는 디렉토리의 가장 마지막을 콘텍스트로 가져간다.
     *                      - callable: 인자로 FQCN, 검색자를 받는다. 반드시 콘텍스트인 문자열을 리턴해야 한다.
     *                                  e.g. function( $fqcn, $finder ) { return 'SomeContext'; }
     *
     *                      예를 들어 '/home/john/logger/src' 가 root path 이고,
     *                      이 root path 에 매핑된 네임브페이스가 'John\Logger'라고 하자.
     *
     *                      /home/john/logger/src/One.php
     *                      /home/john/logger/src/Admin/Admin.php
     *                      /home/john/logger/src/SiteOne/Admin/TrLog.php
     *                      /home/john/logger/src/SiteTwo/Admin/TrLog.php
     *
     *                      이 경로에서 규칙이 'head' 라면 콘텍스트는 공백, Admin, SiteOne, SiteTwo 가 된다.
     *                      반면 규칙이 'tail'이라면 콘텍스트는 공백, Admin 이 된다.
     *
     *                      일반적으로 싱글 사이트라면 head 가 직관적이다.
     *                      그러나 멀티사이트나, 아니면 플러그인 내부에서 보다 세분화된 기능으로 모듈화하여
     *                      플러그인을 구성하는 경우 tail 은 생각할 만한 옵션이다.
     *
     *                      /wordpress/wp-content/plugins/my-plugin/src/AppA/Initiators/Front/FrontInitiator.php
     *                      /wordpress/wp-content/plugins/my-plugin/src/AppA/Initiators/Ajax/AjaxInitiator.php
     *                      /wordpress/wp-content/plugins/my-plugin/src/AppB/Initiators/Admin/AdminInitiator.php
     *                      /wordpress/wp-content/plugins/my-plugin/src/AppB/Initiators/Cron/CronInitiator.php
     *
     *                      이렇게 구성되었다고 보자. 프로그래머는 플러그인을 AppA, AppB 라는 모듈로
     *                      플러그인의 기능을 분리했다. 콘텍스트 규칙이 tail 이라면 실행 콘텍스트는
     *                      Admin, Ajax, Admin, Cron 으로 개시자에서 기본으로 지원하는 콘택스트로 간편하게 그룹화가
     *                      가능하다.
     */
    private $contextRule = 'head';

    public function find(array &$foundClasses)
    {
        if ($this->checkFindCondition()) {
            $rootPath = $this->getRootPath();
            $postfix  = $this->getComponentPostfix();
            if ($rootPath && is_dir($rootPath) && $postfix) {
                $iterator = new RegexIterator(
                    new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootPath)),
                    "/.+{$postfix}\.php$/",
                    RecursiveRegexIterator::MATCH
                );
                /** @var SplFileInfo $item */
                foreach ($iterator as $item) {
                    list($context, $fqcn) = $this->extractContextFqcn($item);
                    if (!isset($foundClasses[$context])) {
                        $foundClasses[$context] = [];
                    }
                    $foundClasses[$context][] = $fqcn;
                }
            }
        }
    }

    /**
     * 검색 대상 경로를 반환
     *
     * @return string
     */
    public function getRootPath(): string
    {
        return $this->rootPath;
    }

    /**
     * 검색 대상 경로를 지정
     *
     * @param string $rootPath
     *
     * @return self
     */
    public function setRootPath(string $rootPath)
    {
        $this->rootPath = untrailingslashit($rootPath);

        return $this;
    }

    /**
     * 기본 네임스페이스를 반환.
     *
     * @return string
     */
    public function getRootNamespace(): string
    {
        return $this->rootNamespace;
    }

    /**
     * 기본 네임스페이스를 지정.
     *
     * @param string $namespace
     *
     * @return self
     */
    public function setRootNamespace(string $namespace)
    {
        $this->rootNamespace = trim($namespace, '\\') . '\\';

        return $this;
    }

    /**
     * 콘텍스트 규칙을 지정한다.
     *
     * @param string|callable $contextRule
     *
     * @return self
     */
    public function setContextRule($contextRule)
    {
        $this->contextRule = $contextRule;

        return $this;
    }

    /**
     * 파일 정보로부터 콘텍스트와 FQCN 문자열을 계산해낸다.
     *
     * @param SplFileInfo $info
     *
     * @return array|false 길이 2인 배열. 0번째는 콘텍스트, 1번째는 FQCN. 에러시 false 리턴.
     */
    protected function extractContextFqcn(SplFileInfo $info)
    {
        $realPath  = $info->getRealPath();
        $basename  = $info->getBasename();
        $className = pathinfo($basename, PATHINFO_FILENAME);

        $rootPathLen   = strlen($this->getRootPath());
        $restNamespace = str_replace(
            DIRECTORY_SEPARATOR,
            '\\',
            substr($realPath, $rootPathLen + 1, strlen($realPath) - $rootPathLen - strlen($basename) - 1)
        );

        $context           = '';
        $firstBackslashPos = strpos($restNamespace, '\\');
        $fqcn              = "{$this->getRootNamespace()}{$restNamespace}{$className}";

        if (false !== $firstBackslashPos) {
            if (is_string($this->contextRule) && !is_callable($this->contextRule)) {
                switch ($this->contextRule) {
                    case 'head':
                        $context = substr($restNamespace, 0, $firstBackslashPos);
                        break;
                    case 'tail':
                        $lastPartPost = strrpos($restNamespace, '\\', -2);
                        if (false === $lastPartPost) {
                            $context = trim($restNamespace, '\\');
                        } else {
                            $context = trim(substr($restNamespace, $lastPartPost + 1), '\\');
                        }
                        break;
                }
            } elseif (is_callable($this->contextRule)) {
                $context = call_user_func_array($this->contextRule, [$fqcn, $this]);
            }
        }

        return [$context, $fqcn];
    }
}
