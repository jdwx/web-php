<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Pages;


use JDWX\Web\Pages\SimpleBinaryPage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( SimpleBinaryPage::class )]
class SimpleBinaryPageTest extends TestCase {


    public function testContentType() : void {
        $page = new SimpleBinaryPage( 'TEST_CONTENT', 'foo/bar' );
        self::assertSame( 'foo/bar', $page->getContentType() );

    }


    public function testToString() : void {
        $page = new SimpleBinaryPage( 'TEST_CONTENT' );
        self::assertSame( 'TEST_CONTENT', (string) $page );
    }


}
