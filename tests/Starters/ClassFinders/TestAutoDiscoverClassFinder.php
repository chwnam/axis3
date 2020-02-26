<?php

namespace Shoplic\Axis3\Tests\Starters\ClassFinders;

use Shoplic\Axis3\Starters\ClassFinders\AutoDiscoverClassFinder;
use WP_UnitTestCase;

use function Shoplic\Axis3\Functions\rmdirRecursive;

class TestAutoDiscoverClassFinder extends WP_UnitTestCase
{
    /**
     * extractContextFqcn() 테스트
     *
     * @see AutoDiscoverClassFinder::extractContextFqcn()
     */
    public function testExtractContextFqcn()
    {
        $finder = new AutoDiscoverClassFinder();
        $finder->setRootPath(dirname(AXIS3_MAIN) . '/tests');
        $finder->setRootNamespace('Shoplic\\Axis3\\Tests\\');

        $reflection = makeMethodAccessible(AutoDiscoverClassFinder::class, 'extractContextFqcn');
        $info       = new \SplFileInfo(__FILE__);

        // 테스트 #1: context rule head 로 하고 이 파일을 테스트
        $output = $reflection->invoke($finder, $info);
        // 검증 #1
        $this->assertEquals('Starters', $output[0]);
        $this->assertEquals(TestAutoDiscoverClassFinder::class, $output[1]);

        // 테스트 #3: context rule 콜백으로 하고 이 파일을 테스트
        $finder->setContextRule(function () {
            return 'MyContext';
        });
        $output = $reflection->invoke($finder, $info);
        // 검증 #3
        $this->assertEquals('MyContext', $output[0]);
        $this->assertEquals(TestAutoDiscoverClassFinder::class, $output[1]);
    }

    /**
     * find() 메소드 테스트
     *
     * @see AutoDiscoverClassFinder::find()
     */
    public function testFind()
    {
        // 가짜 파일을 만들어 본다.
        // find() 메소드는 파일 내용은 신경쓰지 않는다.
        $basePath = __DIR__ . '/TestDir/Temp';
        if (!file_exists($basePath)) {
            mkdir($basePath);
        }

        $files = [
            '/Initiators/Admin/readme.txt',
            '/Initiators/Admin/AdminPostInitiator.php',
            '/Initiators/Admin/CustomPostPostInitiator.php',
            '/Initiators/Ajax/UserAjaxInitiator.php',
            '/Initiators/Ajax/PartyAjaxInitiator.php',
            '/Initiators/Cron/CronInitiator.php',
            '/Initiators/Front/FrontInitiator.php',
            '/Initiators/Front/UserPageInitiator.php',
            '/Initiators/Front/LICENSE',
            '/Initiators/GenericInitiator.php',
            '/Initiators/ConnectionInitiator.php',
            '/Initiators/WronglyPlacedModel.php',
            '/Initiators/.hidden.config',
            '/Initiators/readme.txt',
        ];
        foreach ($files as $file) {
            $fullPath = $basePath . $file;
            if (!file_exists(dirname($fullPath))) {
                mkdir(dirname($fullPath), 0777, true);
            }
            touch($fullPath);
        }

        // 테스트 #1: Initiator 디렉토리를 대상으로 검색
        $foundClasses = [];
        $finder       = new AutoDiscoverClassFinder();
        $finder->setRootPath(__DIR__ . '/TestDir/Temp/Initiators');
        $finder->setRootNamespace('Shoplic\\Axis3\\Tests\\Temp\\Initiators\\');
        $finder->setComponentPostfix('Initiator');
        $finder->find($foundClasses);

        // 검증 #1: 테스트 디렉토리에서 콘텍스트, 클래스를 찾았는지 검증
        $this->assertArrayHasKey('', $foundClasses);
        sort($foundClasses['']);
        $this->assertEquals(
            [
                'Shoplic\\Axis3\\Tests\\Temp\\Initiators\\ConnectionInitiator',
                'Shoplic\\Axis3\\Tests\\Temp\\Initiators\\GenericInitiator',
            ],
            $foundClasses['']
        );

        $this->assertArrayHasKey('Admin', $foundClasses);
        sort($foundClasses['Admin']);
        $this->assertEquals(
            [
                'Shoplic\\Axis3\\Tests\\Temp\\Initiators\\Admin\\AdminPostInitiator',
                'Shoplic\\Axis3\\Tests\\Temp\\Initiators\\Admin\\CustomPostPostInitiator',
            ],
            $foundClasses['Admin']
        );

        $this->assertArrayHasKey('Ajax', $foundClasses);
        sort($foundClasses['Ajax']);
        $this->assertEquals(
            [
                'Shoplic\\Axis3\\Tests\\Temp\\Initiators\\Ajax\\PartyAjaxInitiator',
                'Shoplic\\Axis3\\Tests\\Temp\\Initiators\\Ajax\\UserAjaxInitiator',
            ],
            $foundClasses['Ajax']
        );

        $this->assertArrayHasKey('Cron', $foundClasses);
        sort($foundClasses['Cron']);
        $this->assertEquals(
            [
                'Shoplic\\Axis3\\Tests\\Temp\\Initiators\\Cron\\CronInitiator',
            ],
            $foundClasses['Cron']
        );

        $this->assertArrayHasKey('Front', $foundClasses);
        sort($foundClasses['Front']);
        $this->assertEquals(
            [
                'Shoplic\\Axis3\\Tests\\Temp\\Initiators\\Front\\FrontInitiator',
                'Shoplic\\Axis3\\Tests\\Temp\\Initiators\\Front\\UserPageInitiator',
            ],
            $foundClasses['Front']
        );
    }

    public static function tearDownAfterClass()
    {
        self::cleanupTestDir();
    }

    private static function cleanupTestDir()
    {
        $path = __DIR__ . '/TestDir/Temp';

        if (file_exists($path)) {
            rmdirRecursive($path);
        }
    }
}
