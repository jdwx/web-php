<?php


declare( strict_types = 1 );


namespace Pages;


use JDWX\Web\Pages\AbstractHtmlPage;
use JDWX\Web\Pages\SimpleHtmlPage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( AbstractHtmlPage::class )]
#[CoversClass( SimpleHtmlPage::class )]
final class SimpleHtmlPageTest extends TestCase {


    public function testAddContent() : void {
        $page = new SimpleHtmlPage();
        $page->addContent( 'TEST' );
        $page->addContent( '_' );
        $page->addContent( 'CONTENT' );
        self::assertSame( 'TEST_CONTENT', $page->getContent() );
    }


    public function testCharsetForNoCharset() : void {
        $page = new class() extends SimpleHtmlPage {


            public const ?string DEFAULT_CHARSET = null;


            public function charsetPeek() : string {
                return $this->charset();
            }


        };
        self::assertSame( '', $page->charsetPeek() );
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


    public function testSetLanguage() : void {
        $page = new SimpleHtmlPage();
        self::assertSame( 'en', $page->getLanguage() );
        $page->setLanguage( 'foo' );
        self::assertSame( 'foo', $page->getLanguage() );

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


    public function testTitle() : void {
        $page = new class() extends SimpleHtmlPage {


            public function titlePeek() : string {
                return $this->title();
            }


        };
        self::assertNull( $page->getTitle() );
        self::assertSame( '', $page->titlePeek() );
        $page->setTitle( 'TEST_TITLE' );
        self::assertSame( 'TEST_TITLE', $page->getTitle() );
        self::assertSame( '<title>TEST_TITLE</title>', $page->titlePeek() );
    }


}
