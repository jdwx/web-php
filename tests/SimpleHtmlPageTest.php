<?php


declare( strict_types = 1 );


use JDWX\Web\SimpleHtmlPage;
use PHPUnit\Framework\TestCase;


class SimpleHtmlPageTest extends TestCase {


    /** @noinspection HtmlUnknownTarget */
    public function testAddCSS() : void {
        $page = new SimpleHtmlPage();
        $page->addCSS( 'TEST_CSS' );
        self::assertStringContainsString( '<link rel="stylesheet" href="TEST_CSS">', $page->render() );
    }


    public function testAddContent() : void {
        $page = new SimpleHtmlPage();
        $page->addContent( 'TEST' );
        $page->addContent( '_' );
        $page->addContent( 'CONTENT' );
        self::assertStringContainsString( 'TEST_CONTENT', $page->render() );
    }


    public function testEcho() : void {
        $page = new SimpleHtmlPage();
        $page->setContent( 'TEST_CONTENT' );
        ob_start();
        $page->echo();
        $result = ob_get_clean();
        self::assertStringContainsString( 'TEST_CONTENT', $result );
    }


    public function testRender() : void {
        $page = new SimpleHtmlPage();
        $page->setContent( 'TEST_CONTENT' );

        # Test for doctype.
        self::assertStringContainsString( '<!DOCTYPE html>', $page->render() );

        # Test for language.
        self::assertStringContainsString( '<html lang="en">', $page->render() );

        # Test for viewport.
        self::assertStringContainsString( 'width=device-width', $page->render() );

    }


    public function testSetCharset() : void {
        $page = new SimpleHtmlPage();
        $page->setCharset( 'TEST_CHARSET' );
        self::assertStringContainsString( '<meta charset="TEST_CHARSET">', $page->render() );
    }


    public function testSetContent() : void {
        $page = new SimpleHtmlPage();
        $page->setContent( 'TEST_CONTENT' );
        self::assertStringContainsString( 'TEST_CONTENT', $page->render() );
    }


    public function testTitle() : void {
        $page = new SimpleHtmlPage();
        $page->setTitle( 'TEST_TITLE' );
        self::assertStringContainsString( '<title>TEST_TITLE</title>', $page->render() );
    }


}
