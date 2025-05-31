<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Stream;


use JDWX\Web\Stream\SimpleStringable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( SimpleStringable::class )]
final class SimpleStringableTest extends TestCase {


    public function testToString() : void {
        $st = new SimpleStringable( 'Foo' );
        self::assertSame( 'Foo', strval( $st ) );
    }


}
