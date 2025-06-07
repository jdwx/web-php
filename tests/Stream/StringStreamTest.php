<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Stream;


use JDWX\Web\Stream\StringStream;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( StringStream::class )]
final class StringStreamTest extends TestCase {


    public function testAsArray() : void {
        $sst = new StringStream( 'foo' );
        self::assertSame( [ 'foo' ], $sst->asList() );
    }


    public function testStreamStrings() : void {
        $sst = new StringStream( 'foo' );
        self::assertSame( [ 'foo' ], iterator_to_array( $sst->streamStrings(), false ) );
    }


}
