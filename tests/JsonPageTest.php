<?php


declare( strict_types = 1 );


use JDWX\Web\JsonPage;


#[\PHPUnit\Framework\Attributes\CoversClass(JsonPage::class)]
final class JsonPageTest extends \PHPUnit\Framework\TestCase {


    public function testRender() : void {
        $page = new JsonPage( 'TEST_CONTENT' );
        self::assertSame( '"TEST_CONTENT"', $page->render() );

        $page = new JsonPage( [ 'foo' => 'bar' ] );
        self::assertSame( '{"foo":"bar"}', $page->render() );

        $page = new JsonPage( [ 'foo' => 'bar' ], true );
        self::assertSame( "{\n    \"foo\": \"bar\"\n}", $page->render() );
    }

}