<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Pages;


use JDWX\Web\Pages\AbstractTextPage;
use JDWX\Web\Pages\SimpleTextPage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( AbstractTextPage::class )]
#[CoversClass( SimpleTextPage::class )]
final class SimpleTextPageTest extends TestCase {


    public function testRender() : void {
        $page = new SimpleTextPage( 'TEST_CONTENT' );
        self::assertSame( 'TEST_CONTENT', $page->render() );
    }


}