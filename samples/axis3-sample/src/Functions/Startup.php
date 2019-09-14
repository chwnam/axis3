<?php

namespace Shoplic\Axis3Sample\Functions;

use Exception;
use Shoplic\Axis3\Starters\ClassFinders\AutoDiscoverClassFinder;
use Shoplic\Axis3\Starters\Starter;

function checkEnvironment($requiredPhpVersion, $minimumAxisVersion)
{
    $phpVersion  = version_compare(phpversion(), $requiredPhpVersion, '>=');
    $axisVersion = version_compare(AXIS3_VERSION, $minimumAxisVersion, '>=');

    return $phpVersion && $axisVersion;
}

/**
 * @param $mainFile
 * @param $version
 *
 * @return Starter
 * @throws Exception
 */
function getStarter($mainFile, $version)
{
    $requiredPhpVersion = '7.0';
    $minimumAxisVersion = '1.0.0';

    if (!checkEnvironment($requiredPhpVersion, $minimumAxisVersion)) {
        throw new Exception(
            sprintf(
                __('Version mismatch! PHP version should be %s+ and the Axis 3 should be %s+.', 'axis3'),
                $requiredPhpVersion,
                $minimumAxisVersion
            )
        );
    }

    return (new Starter())
        ->addClassFinder(
            (new AutoDiscoverClassFinder())
                ->setComponentPostfix('Initiator')
                ->setRootPath(dirname($mainFile) . '/src/Initiators')
                ->setRootNamespace('Shoplic\\Axis3Sample\\Initiators\\')
        )
        ->addClassFinder(
            (new AutoDiscoverClassFinder())
                ->setComponentPostfix('Model')
                ->setRootPath(dirname($mainFile) . '/src/Models')
                ->setRootNamespace('Shoplic\\Axis3Sample\\Models\\')
        )
        ->setMainFile($mainFile)
        ->setVersion($version)
        ->setPrefix('axis3_sample')
        ->setSlug('axis3-sample');
}
