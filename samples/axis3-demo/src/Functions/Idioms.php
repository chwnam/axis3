<?php

namespace Shoplic\Axis3Sample\Functions;

use Shoplic\Axis3\Interfaces\Starters\StarterInterface;
use Shoplic\Axis3\Starters\StarterPool;

function getStarter(): StarterInterface
{
    static $starter = null;

    if (is_null($starter)) {
        $starter = StarterPool::getInstance()->getStarter('axis3-demo');
    }

    return $starter;
}

function getPrefix(bool $useHyphen = false): string
{
    static $prefix = null;

    if (is_null($prefix)) {
        $prefix = substr(getStarter()->getPrefix(), 0, -1);
    }

    return $prefix . ($useHyphen ? '-' : '_');
}

function prefixed(string $str, bool $useHyphen = false): string
{
    return getPrefix($useHyphen) . $str;
}
