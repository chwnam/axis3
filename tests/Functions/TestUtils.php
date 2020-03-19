<?php

use function Shoplic\Axis3\Functions\josa;
use function Shoplic\Axis3\Functions\splitHangul;
use function Shoplic\Axis3\Functions\strSplit;

class TestUtils extends WP_UnitTestCase
{
    public function test_strSplit()
    {
        $input = '안녕하세요';

        $this->assertEquals(['안', '녕', '하', '세', '요'], strSplit($input));
    }

    public function test_splitHangul()
    {
        $this->assertEquals(
            [
                ['ㄺ', '', ''],
                ['ㄻ', '', ''],
                ['ㄼ', '', ''],
            ],
            splitHangul('ㄺㄻㄼ')
        );

        $this->assertEquals(
            [
                ['ㅎ', '', ''],
                ['ㅏ', '', ''],
                ['ㄴ', '', ''],
                ' ',
                ['ㄱ', 'ㅡ', 'ㄹ'],
            ],
            splitHangul('ㅎㅏㄴ 글')
        );

        $this->assertEquals(
            [
                ['ㅇ', 'ㅏ', 'ㄴ'],
                ['ㄴ', 'ㅕ', 'ㅇ'],
                ['ㅎ', 'ㅏ', ''],
                ['ㅅ', 'ㅔ', ''],
                ['ㅇ', 'ㅛ', ''],
            ],
            splitHangul('안녕하세요')
        );
    }

    public function test_josa()
    {
        $this->assertEquals(
            josa('MBC', '을', '를'),
            'MBC를'
        );

        $this->assertEquals(
            josa('한글2019', '을', '를'),
            '한글2019를'
        );

        $this->assertEquals(
            josa('한글2018', '을', '를'),
            '한글2018을'
        );

        $this->assertEquals(
            josa('사과', '이', '가'),
            '사과가'
        );
    }
}
