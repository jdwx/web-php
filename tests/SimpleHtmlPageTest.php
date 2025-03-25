<?php


declare( strict_types = 1 );


use JDWX\Web\SimpleHtmlPage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( SimpleHtmlPage::class )]
final class SimpleHtmlPageTest extends TestCase {


    public function testAddContent() : void {
        $page = new SimpleHtmlPage();
        $page->addContent( 'TEST' );
        $page->addContent( '_' );
        $page->addContent( 'CONTENT' );
        self::assertSame( 'TEST_CONTENT', $page->getContent() );
    }


    public function testPrefix() : void {
        $page = new class( 'CONTENT' ) extends SimpleHtmlPage {


            public function __construct( ?string $i_nstContent ) {
                parent::__construct( $i_nstContent );
            }


            protected function prefix() : string {
                return 'TEST_';
            }


        };
        self::assertStringContainsString( 'TEST_CONTENT', $page->render() );
    }


    public function testPrependContent() : void {
        $page = new SimpleHtmlPage();
        $page->setContent( 'CONTENT' );
        $page->prependContent( 'TEST_' );
        self::assertSame( 'TEST_CONTENT', $page->getContent() );
    }


    public function testRender() : void {
        $page = new SimpleHtmlPage();
        $page->setContent( 'TEST_CONTENT' );
        self::assertStringContainsString( '<body>TEST_CONTENT</body>', $page->render() );
    }


    public function testSetContent() : void {
        $page = new SimpleHtmlPage();
        $page->setContent( 'TEST_CONTENT' );
        self::assertSame( 'TEST_CONTENT', $page->getContent() );
    }


    public function testSuffix() : void {
        $page = new class( 'CONTENT' ) extends SimpleHtmlPage {


            public function __construct( ?string $i_nstContent ) {
                parent::__construct( $i_nstContent );
            }


            protected function suffix() : string {
                return '_TEST';
            }


        };
        self::assertStringContainsString( 'CONTENT_TEST', $page->render() );
    }


}
