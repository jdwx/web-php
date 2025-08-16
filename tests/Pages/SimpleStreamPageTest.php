<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Pages;


use JDWX\Web\Pages\AbstractStreamPage;
use JDWX\Web\Pages\SimpleStreamPage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( SimpleStreamPage::class )]
#[CoversClass( AbstractStreamPage::class )]
final class SimpleStreamPageTest extends TestCase {


    /** @suppress PhanTypeInvalidDimOffset, PhanTypeMismatchArgument */
    public function testStream() : void {
        $page = new SimpleStreamPage( $this->gen() );
        $r = [];
        $fn = static function ( $stChunk ) use ( &$r ) {
            $r[] = $stChunk;
        };
        ob_start( $fn );
        $page->echo();
        ob_end_clean();
        self::assertStringContainsString( 'event: foo', $r[ 0 ] );
        self::assertStringContainsString( 'data: {"bar":"baz"}', $r[ 0 ] );
        self::assertStringContainsString( 'event: qux', $r[ 1 ] );
        self::assertStringContainsString( 'data: 5', $r[ 1 ] );
        self::assertSame( '', $r[ 2 ] );
        self::assertCount( 3, $r );
    }


    private function gen() : \Generator {
        yield 'foo' => [ 'bar' => 'baz' ];
        yield 'qux' => 5;
    }


}
