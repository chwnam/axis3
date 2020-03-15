<?php

namespace Shoplic\Axis3\Cli;

use Shoplic\Axis3\Interfaces\Starters\StarterInterface;

use function Shoplic\Axis3\Functions\strStartsWith;

/**
 * Class PluginInspector
 *
 * 플러그인 검사 부모 클래스.
 *
 * @package Shoplic\Axis3\Cli
 */
abstract class PluginInspector
{
    /** @var StarterInterface */
    private $stater = null;

    private $classes = [];

    public function __construct(StarterInterface $starter)
    {
        $this->stater = $starter;
    }

    public function getStarter(): StarterInterface
    {
        return $this->stater;
    }

    /**
     * 클래스 문서 주석을 적절히 추출.
     *
     * 첫번째로 만나는 Class <class_name> 부분을 삭제한다.
     * 만약 추출물에 이 문구가 그대로 출력되면 실제 클래스이름과 맞지 않아서 그럴 것이다.
     *
     * @param string $comment     주석 부분. \/** .. *\/ 로 되어 있을 것이다.
     * @param string $className   이 클래스의 FQN.
     *
     * @return string
     */
    public function cleanupClassDocComment(string $comment, string $className): string
    {
        $comment = str_replace("\r", "\n", $comment);
        $pos     = strrpos($className, '\\');
        $cls     = trim(substr($className, $pos === false ? 0 : $pos + 1), '\\');
        $lines   = [];

        preg_match_all('/^[ \t\/*]*(.*)$/miu', $comment, $matches, PREG_SET_ORDER, 0);
        foreach ($matches as $match) {
            $m = trim($match[1]);
            if (strStartsWith($m, '@')) {
                continue;
            }
            $lines[] = $m;
        }

        while (!empty($lines) && empty($lines[0])) {
            array_shift($lines);
        }
        if ($lines && preg_match("/^Class\s+{$cls}$/", $lines[0])) {
            array_shift($lines);
        }

        $lines = trim(preg_replace("\n{2,}", "\n", implode("\n", $lines)));

        return $lines . ($lines ? "\n\n" : '');
    }

    abstract public function inspect();
}
