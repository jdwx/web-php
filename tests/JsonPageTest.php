<?php


declare( strict_types = 1 );


use JDWX\Web\JsonPage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass(JsonPage::class)]
final class JsonPageTest extends TestCase {


    public function testRender() : void {
        $page = new JsonPage( 'TEST_CONTENT' );
        self::assertSame( "\"TEST_CONTENT\"\n", $page->render() );

        $page = new JsonPage( [ 'foo' => 'bar' ] );
        self::assertSame( "{\"foo\":\"bar\"}\n", $page->render() );

        $page = new JsonPage( [ 'foo' => 'bar' ], true );
        self::assertSame( "{\n    \"foo\": \"bar\"\n}\n", $page->render() );
    }

}