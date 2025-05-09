<?php


declare( strict_types = 1 );


use JDWX\Web\HtmlPage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( HtmlPage::class )]
final class HtmlPageTest extends TestCase {


    public function testAddCSSUri() : void {
        $page = $this->newHtmlPage();
        $page->addCssUri( 'TEST_CSS' );
        $st = $page->render();
        self::assertMatchesRegularExpression( '#<head>.*TEST_CSS.*</head>#', $st );
    }


    public function testGetCharset() : void {
        $page = $this->newHtmlPage();
        self::assertSame( 'UTF-8', $page->getCharset() );

        $page->setCharset( 'foo' );
        self::assertSame( 'foo', $page->getCharset() );

        $page->setCharset( null );
        self::assertNull( $page->getCharset() );
    }


    public function testGetDefaultLanguage() : void {
        $page = $this->newHtmlPage();
        self::assertSame( 'en', $page->getLanguage() );
        $page->setLanguage( 'foo' );
        self::assertSame( 'foo', $page->getLanguage() );

        $page = $this->newHtmlPage( '', 'bar' );
        self::assertSame( 'bar', $page->getLanguage() );
    }


    public function testGetTitle() : void {
        $page = $this->newHtmlPage();
        $page->setTitle( 'TEST_TITLE' );
        self::assertSame( 'TEST_TITLE', $page->getTitle() );
    }


    /** @noinspection HtmlUnknownTarget */


    public function testRender() : void {
        $page = $this->newHtmlPage( 'TEST_CONTENT' );
        $page->setTitle( 'TEST_TITLE' );
        $page->addCssUri( 'TEST_CSS' );

        # Test for doctype.
        self::assertStringContainsString( '<!DOCTYPE html>', $page->render() );

        # Test for language.
        self::assertStringContainsString( '<html lang="en">', $page->render() );

        # Test for viewport.
        self::assertStringContainsString( 'width=device-width', $page->render() );

        # Test for title.
        self::assertStringContainsString( '<title>TEST_TITLE</title>', $page->render() );

        # Test for CSS
        self::assertStringContainsString( '<link href="TEST_CSS" rel="stylesheet">', $page->render() );

        # Test for charset.
        self::assertStringContainsString( '<meta charset="UTF-8">', $page->render() );

        # Test for content.
        self::assertStringContainsString( '<body>TEST_CONTENT</body>', $page->render() );

    }


    public function testRenderForIterableContent() : void {
        $content = [ 'TEST', '_', 'CONTENT' ];
        $page = $this->newHtmlPage( $content );
        self::assertStringContainsString( '<body>TEST_CONTENT</body>', $page->render() );
    }


    public function testRenderForNoCharset() : void {
        $page = $this->newHtmlPage();
        $page->setCharset( null );
        self::assertStringNotContainsString( '<meta charset="', $page->render() );
    }


    public function testStream() : void {
        $page = $this->newHtmlPage( 'TEST_CONTENT' );
        $st = implode( '', iterator_to_array( $page->stream() ) );
        self::assertSame( $page->render(), $st );
    }


    /**
     * @param string|iterable<string> $i_content
     */
    private function newHtmlPage( string|iterable $i_content = '', ?string $i_nstLanguage = null ) : HtmlPage {
        return new class( $i_nstLanguage, $i_content ) extends HtmlPage {


            /** @param string|iterable<string> $content */
            public function __construct( ?string $i_nstLanguage, private readonly string|iterable $content ) {
                parent::__construct( $i_nstLanguage );
            }


            protected function content() : string|iterable {
                return $this->content;
            }


        };
    }


}
