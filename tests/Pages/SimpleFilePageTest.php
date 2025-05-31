<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Pages;


use JDWX\Web\Pages\AbstractBinaryPage;
use JDWX\Web\Pages\SimpleFilePage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( AbstractBinaryPage::class )]
#[CoversClass( SimpleFilePage::class )]
final class SimpleFilePageTest extends TestCase {


    public function testInferType() : void {
        self::assertSame( 'text/plain', SimpleFilePage::inferType( 'TestFile.txt' ) );
        self::assertSame( 'image/jpeg', SimpleFilePage::inferType( 'TestFile.jpg' ) );
        self::assertNull( SimpleFilePage::inferType( 'TestFile.foo' ) );
        self::assertNull( SimpleFilePage::inferType( 'TestFile' ) );
    }


    public function testRender() : void {
        $page = new SimpleFilePage( __DIR__ . '/../Shims/TestFile.txt' );
        self::assertSame( 'This is a test file.', $page->render() );
    }


}
