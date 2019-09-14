<?php

namespace Shoplic\Axis3Sample\Functions;

function getPrefix(bool $useHyphen = false): string
{
    return 'axis3_sample' . ($useHyphen ? '-' : '_');
}

function prefixed(string $str, bool $useHyphen = false): string
{
    return getPrefix($useHyphen) . $str;
}
