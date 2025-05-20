<?php


declare( strict_types = 1 );


namespace Stream;


use JDWX\Web\Stream\StringStream;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( StringStream::class )]
final class StringStreamTest extends TestCase {


    public function testAsArray() : void {
        $sst = new StringStream( 'foo' );
        self::assertSame( [ 'foo' ], $sst->asArray() );
    }


    public function testGetIterator() : void {
        $sst = new StringStream( 'foo' );
        self::assertSame( [ 'foo' ], iterator_to_array( $sst->getIterator(), false ) );
    }


    public function testStreamStrings() : void {
        $sst = new StringStream( 'foo' );
        self::assertSame( [ 'foo' ], iterator_to_array( $sst->streamStrings(), false ) );
    }


    public function testTraversable() : void {
        $sst = new StringStream( 'foo' );
        $r = [];
        foreach ( $sst as $s ) {
            $r[] = $s;
        }
        self::assertSame( [ 'foo' ], $r );
    }


}
