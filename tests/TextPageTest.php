<?php


declare( strict_types = 1 );


use JDWX\Web\TextPage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( TextPage::class)]
final class TextPageTest extends TestCase {


    public function testRender() : void {
        $page = new JDWX\Web\TextPage( 'TEST_CONTENT' );
        self::assertSame( 'TEST_CONTENT', $page->render() );
    }


}