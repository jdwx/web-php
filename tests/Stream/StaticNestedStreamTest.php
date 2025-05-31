<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Stream;


use JDWX\Web\Stream\AbstractNestedStringableStream;
use JDWX\Web\Stream\StaticNestedStream;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( AbstractNestedStringableStream::class )]
#[CoversClass( StaticNestedStream::class )]
final class StaticNestedStreamTest extends TestCase {


    public function testToString() : void {
        $sst = new StaticNestedStream( 'Bar', 'Foo', 'Baz' );
        self::assertSame( 'FooBarBaz', strval( $sst ) );
    }


}
