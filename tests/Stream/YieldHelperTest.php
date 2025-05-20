<?php


declare( strict_types = 1 );


namespace Stream;


use JDWX\Web\Stream\YieldHelper;
use PHPUnit\Framework\TestCase;
use Stringable;


class YieldHelperTest extends TestCase {


    public function testYield() : void {
        self::assertSame(
            [ 'TEST_CONTENT' ],
            iterator_to_array( YieldHelper::yield( 'TEST_CONTENT' ), false )
        );
        self::assertSame(
            [ 'TEST_CONTENT_1', 'TEST_CONTENT_2' ],
            iterator_to_array( YieldHelper::yield( [ 'TEST_CONTENT_1', 'TEST_CONTENT_2' ] ), false )
        );
        $st = new class() implements Stringable {


            public function __toString() : string {
                return 'TEST_CONTENT_3';
            }


        };
        self::assertSame(
            [ $st ],
            iterator_to_array( YieldHelper::yield( $st ), false )
        );
    }


    public function testYieldDeep() : void {
        self::assertSame(
            [ 'TEST_CONTENT' ],
            iterator_to_array( YieldHelper::yieldDeep( 'TEST_CONTENT' ), false )
        );
        self::assertSame(
            [ 'TEST_CONTENT_1', 'TEST_CONTENT_2' ],
            iterator_to_array( YieldHelper::yieldDeep( [ 'TEST_CONTENT_1', 'TEST_CONTENT_2' ] ), false )
        );
        $st = new class() implements Stringable {


            public function __toString() : string {
                return 'TEST_CONTENT_3';
            }


        };
        self::assertSame(
            [ $st ],
            iterator_to_array( YieldHelper::yieldDeep( $st ), false )
        );
    }


}
