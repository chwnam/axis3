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
