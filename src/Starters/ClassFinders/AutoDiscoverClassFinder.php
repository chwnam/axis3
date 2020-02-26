<?php

namespace Shoplic\Axis3\Starters\ClassFinders;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;
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
    /**
     * 검색 경로의 짝. 키는 경로, 값은 네임스페이스.
     *
     * @var array
     */
    private $rootPairs = [];

    public function find(array &$foundClasses)
    {
        if ($this->checkFindCondition()) {
            reset($this->rootPairs);
            while ($this->hasPairs()) {
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
                $this->nextPair();
            }
        }
    }

    public function addRootPairs(string $rootNamespace, string $rootPath)
    {
        if ($rootNamespace && $rootPath) {
            $this->rootPairs[$rootPath] = $rootNamespace;
        }

        return $this;
    }

    /**
     * 검색 대상 경로를 반환
     *
     * @return string
     */
    public function getRootPath(): string
    {
        return key($this->rootPairs);
    }

    /**
     * 기본 네임스페이스를 반환.
     *
     * @return string
     */
    public function getRootNamespace(): string
    {
        return current($this->rootPairs);
    }

    protected function hasPairs()
    {
        return false !== current($this->rootPairs);
    }

    protected function nextPair()
    {
        return next($this->rootPairs);
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
            $context = substr($restNamespace, 0, $firstBackslashPos);
        }

        return [$context, $fqcn];
    }
}
