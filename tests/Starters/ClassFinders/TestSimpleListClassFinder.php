<?php

namespace Shoplic\Axis3\Tests\Starters\ClassFinders;

use Shoplic\Axis3\Starters\ClassFinders\SimpleListClassFinder;
use WP_UnitTestCase;

class TestSimpleListClassFinder extends WP_UnitTestCase
{
    private $finder;

    public function testFindAddClasses()
    {
        $finder = new SimpleListClassFinder();

        // 셋업 #1: 2개의 클래스를 삽입.
        $finder->addClasses(
            'default',
            [
                '\\My\\TestClass\\TestClassOne',
                '\\My\\TestClass\\TestClassTwo',
            ]
        );
        $foundClass = [];
        $finder->find($foundClass);

        // 검증 #1: 삽입된 2개의 클래스를 확인.
        $this->assertArrayHasKey('default', $foundClass);
        $this->assertIsArray($foundClass['default']);
        $this->assertEquals(2, sizeof($foundClass['default']));
        // 네임스페이스 가장 앞의 백슬래시는 제거되어야 한다.
        $this->assertEquals('My\\TestClass\\TestClassOne', $foundClass['default'][0]);
        $this->assertEquals('My\\TestClass\\TestClassTwo', $foundClass['default'][1]);

        // 셋업 #2: 2개의 클래스를 추가로 삽입.
        $finder->addClasses(
            'default',
            [
                'My\\TestClass\\TestClassThree',
                'My\\TestClass\\TestClassFour',
            ]
        );
        $foundClass = [];
        $finder->find($foundClass);

        // 검증 #2: 추가된 2개의 클래스를 합쳐 도합 4개의 클래스가 찾아져야 한다.
        $this->assertArrayHasKey('default', $foundClass);
        $this->assertIsArray($foundClass['default']);
        $this->assertEquals(4, sizeof($foundClass['default']));
        $this->assertEquals('My\\TestClass\\TestClassThree', $foundClass['default'][2]);
        $this->assertEquals('My\\TestClass\\TestClassFour', $foundClass['default'][3]);

        // 셋업 #3: $foundClass 에 이미 값이 있다.
        $foundClass = [
            'default' => [
                'My\\TestClass\TestClassAlpha',
                'My\\TestClass\TestClassBravo',
            ]
        ];
        $finder->find($foundClass);

        // 검증 #3: $foundClass 에 이미 들어간 값을 보존하면서 새 값을 추가해야 한다.
        //          그러므로 도합 6개의 클래스가 찾아져야 한다.
        $this->assertArrayHasKey('default', $foundClass);
        $this->assertIsArray($foundClass['default']);
        $this->assertEquals(6, sizeof($foundClass['default']));
        $this->assertEquals('My\\TestClass\\TestClassAlpha', $foundClass['default'][0]);
        $this->assertEquals('My\\TestClass\\TestClassBravo', $foundClass['default'][1]);
        $this->assertEquals('My\\TestClass\\TestClassOne', $foundClass['default'][2]);
        $this->assertEquals('My\\TestClass\\TestClassTwo', $foundClass['default'][3]);
        $this->assertEquals('My\\TestClass\\TestClassThree', $foundClass['default'][4]);
        $this->assertEquals('My\\TestClass\\TestClassFour', $foundClass['default'][5]);
    }
}
