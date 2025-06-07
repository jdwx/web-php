<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Stream;


use JDWX\Web\Stream\SimpleStringable;
use JDWX\Web\Stream\StreamHelper;
use JDWX\Web\Stream\StringableList;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Stringable;


#[CoversClass( StreamHelper::class )]
final class StreamHelperTest extends TestCase {


    public function testAsListForIterable() : void {
        $r = [ 'Foo', 'Bar', new SimpleStringable( 'Baz' ) ];
        self::assertSame( $r, StreamHelper::asList( $r ) );
    }


    public function testAsListForIterableNested() : void {
        $r = [ 'Foo', [ 'Bar', [ 'Baz', 'Qux' ] ] ];
        self::assertSame( [ 'Foo', 'Bar', 'Baz', 'Qux' ], StreamHelper::asList( $r ) );
    }


    public function testAsListForStream() : void {
        $baz = new SimpleStringable( 'Baz' );
        $stl = new StringableList( [ 'Foo', 'Bar', $baz ] );
        self::assertSame( [ 'Foo', 'Bar', $baz ], StreamHelper::asList( $stl ) );
    }


    public function testAsListForString() : void {
        self::assertSame( [ 'Foo' ], StreamHelper::asList( 'Foo' ) );
    }


    public function testAsListForStringable() : void {
        $str = new SimpleStringable( 'Foo' );
        self::assertSame( [ $str ], StreamHelper::asList( $str ) );
    }


    public function testToStringForIterable() : void {
        self::assertSame( 'FooBarBaz', StreamHelper::toString( [ 'Foo', 'Bar', 'Baz' ] ) );
    }


    public function testToStringForIterableNested() : void {
        $r = [ 'Foo', [ 'Bar', [ 'Baz', 'Qux' ] ] ];
        self::assertSame( 'FooBarBazQux', StreamHelper::toString( $r ) );
    }


    public function testToStringForStream() : void {
        $stl = new StringableList( [ 'Foo', 'Bar', new SimpleStringable( 'Baz' ) ] );
        self::assertSame( 'FooBarBaz', StreamHelper::toString( $stl ) );
    }


    public function testToStringForString() : void {
        self::assertSame( 'Foo', StreamHelper::toString( 'Foo' ) );
    }


    public function testToStringForStringable() : void {
        $str = new SimpleStringable( 'Foo' );
        self::assertSame( 'Foo', StreamHelper::toString( $str ) );
    }


    public function testYield() : void {
        self::assertSame(
            [ 'TEST_CONTENT' ],
            iterator_to_array( StreamHelper::yield( 'TEST_CONTENT' ), false )
        );
        self::assertSame(
            [ 'TEST_CONTENT_1', 'TEST_CONTENT_2' ],
            iterator_to_array( StreamHelper::yield( [ 'TEST_CONTENT_1', 'TEST_CONTENT_2' ] ), false )
        );
        $st = new class() implements Stringable {


            public function __toString() : string {
                return 'TEST_CONTENT_3';
            }


        };
        self::assertSame(
            [ $st ],
            iterator_to_array( StreamHelper::yield( $st ), false )
        );
    }


    public function testYieldDeep() : void {
        self::assertSame(
            [ 'TEST_CONTENT' ],
            iterator_to_array( StreamHelper::yieldDeep( 'TEST_CONTENT' ), false )
        );
        self::assertSame(
            [ 'TEST_CONTENT_1', 'TEST_CONTENT_2' ],
            iterator_to_array( StreamHelper::yieldDeep( [ 'TEST_CONTENT_1', 'TEST_CONTENT_2' ] ), false )
        );
        $st = new class() implements Stringable {


            public function __toString() : string {
                return 'TEST_CONTENT_3';
            }


        };
        self::assertSame(
            [ $st ],
            iterator_to_array( StreamHelper::yieldDeep( $st ), false )
        );
    }


    public function testYieldDeepForNestedStreams() : void {
        $bar = new SimpleStringable( 'Bar' );
        $stl = new StringableList( [ 'Foo', $bar ] );
        $stl2 = new StringableList( [ 'Baz', $stl ] );
        self::assertSame(
            [ 'Baz', 'Foo', $bar ],
            iterator_to_array( StreamHelper::yieldDeep( $stl2 ), false )
        );
    }


}
