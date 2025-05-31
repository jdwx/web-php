<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Pages;


use JDWX\Web\Pages\AbstractJsonPage;
use JDWX\Web\Pages\SimpleJsonPage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( AbstractJsonPage::class )]
#[CoversClass( SimpleJsonPage::class )]
final class SimpleJsonPageTest extends TestCase {


    public function testRender() : void {
        $page = new SimpleJsonPage( 'TEST_CONTENT' );
        self::assertSame( "\"TEST_CONTENT\"\n", $page->render() );

        $page = new SimpleJsonPage( [ 'foo' => 'bar' ] );
        self::assertSame( "{\"foo\":\"bar\"}\n", $page->render() );

        $page = new SimpleJsonPage( [ 'foo' => 'bar' ], true );
        self::assertSame( "{\n    \"foo\": \"bar\"\n}\n", $page->render() );
    }


}