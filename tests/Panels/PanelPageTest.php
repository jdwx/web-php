<?php


declare( strict_types = 1 );


namespace Panels;


use JDWX\Web\Backends\MockHttpBackend;
use JDWX\Web\Http;
use JDWX\Web\Panels\PanelPage;
use JDWX\Web\Panels\ScriptBody;
use JDWX\Web\Panels\ScriptUri;
use JDWX\Web\Panels\SimplePanel;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Shims\MyBodyPanel;


require_once __DIR__ . '/../Shims/MyBodyPanel.php';


#[CoversClass( PanelPage::class )]
final class PanelPageTest extends TestCase {


    public function testAppendPanel() : void {
        $panel1 = new SimplePanel( 'Hello' );
        $panel2 = new SimplePanel( 'World' );
        $page = new PanelPage( [ $panel1 ] );
        $page->appendPanel( $panel2 );
        $st = '<body>HelloWorld</body>';
        self::assertStringContainsString( $st, $page->render() );
    }


    public function testBody() : void {
        $panel1 = new SimplePanel( 'Hello' );
        $panel2 = new SimplePanel( 'World' );
        $page = new PanelPage( [ $panel1, $panel2 ] );
        $st = '<body>HelloWorld</body>';
        self::assertStringContainsString( $st, $page->render() );
    }


    /**
     * @noinspection HtmlUnknownTarget
     */
    public function testCSS() : void {
        $panel1 = new SimplePanel();
        $panel1->addCssUri( '/foo.css' );
        $panel2 = new SimplePanel();
        $panel2->addCssUri( '/bar.css' );
        $page = new PanelPage( [ $panel1, $panel2 ] );
        $st = $page->render();
        self::assertStringContainsString( '<link href="/foo.css" rel="stylesheet">', $st );
        self::assertStringContainsString( '<link href="/bar.css" rel="stylesheet">', $st );

        # Test that the CSS is not duplicated
        $panel2->addCssUri( '/foo.css' );
        $st = $page->render();
        $stCheck = '<meta charset="UTF-8"><link href="/foo.css" rel="stylesheet">'
            . '<link href="/bar.css" rel="stylesheet"></head>';
        self::assertStringContainsString( $stCheck, $st );
    }


    public function testConstructForSinglePanel() : void {
        $panel = new SimplePanel( 'Hello' );
        $page = new PanelPage( $panel );
        $st = '<body>Hello</body>';
        self::assertStringContainsString( $st, $page->render() );
    }


    public function testEmpty() : void {
        $page = new PanelPage();
        $st = "<!DOCTYPE html>\n"
            . "<html lang=\"en\">\n"
            . "<head><meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">"
            . "<meta charset=\"UTF-8\"></head><body></body></html>";
        self::assertSame( $st, $page->render() );
    }


    public function testFirst() : void {
        $st = '';
        $panel1 = new MyBodyPanel();
        $panel1->fnFirst = function () use ( &$st ) {
            $st .= 'Foo';
        };
        $panel2 = new MyBodyPanel();
        $panel2->fnFirst = function () use ( &$st ) {
            $st .= 'Bar';
        };
        $page = new PanelPage( [ $panel1, $panel2 ] );
        $page->render();
        self::assertSame( 'FooBar', $st );
    }


    public function testFullPage() : void {
        $http = new MockHttpBackend();
        Http::init( $http );

        $panel1 = new MyBodyPanel();
        $panel1->addCssUri( '/foo.css' );
        $script1 = new ScriptBody( 'FOO' );
        $script1->setAsync();
        $panel1->addScript( $script1 );
        $panel1->addHeader( 'X-Foo', 'Foo' );
        $panel1->stBodyEarly = 'EarlyFoo';
        $panel1->stBody = 'BodyFoo';
        $panel1->stBodyLate = 'LateFoo';
        $panel1->stHead = 'HeadFoo';

        $panel2 = new MyBodyPanel();
        $panel2->addCssUri( '/bar.css' );
        $script2 = new ScriptUri( '/bar.js' );
        $script2->setDefer();
        $panel2->addScript( $script2 );
        $panel2->addHeader( 'X-Bar', 'Bar' );
        $panel2->stBodyEarly = 'EarlyBar';
        $panel2->stBody = 'BodyBar';
        $panel2->stBodyLate = 'LateBar';
        $panel2->stHead = 'HeadBar';

        $page = new PanelPage( [ $panel1, $panel2 ] );
        $st = $page->render();
        self::assertMatchesRegularExpression( '#<head>.*HeadFoo.*HeadBar.*</head>#s', $st );
        self::assertMatchesRegularExpression(
            '#<body>.*EarlyFoo.*EarlyBar.*BodyFoo.*BodyBar.*LateFoo.*LateBar.*</body>#s',
            $st );
        self::assertMatchesRegularExpression(
            '#<link href="/foo.css" rel="stylesheet">.*<link href="/bar.css" rel="stylesheet">#s',
            $st
        );
        self::assertMatchesRegularExpression(
            '#<script async>FOO</script>.*<script defer src="/bar.js"></script>#s',
            $st
        );
        $rHeaders = iterator_to_array( $page->getHeaders(), false );
        self::assertCount( 3, $rHeaders );
        self::assertSame( 'Content-Type: text/html; charset=UTF-8', $rHeaders[ 0 ] );;
        self::assertSame( 'X-Foo: Foo', $rHeaders[ 1 ] );
        self::assertSame( 'X-Bar: Bar', $rHeaders[ 2 ] );

    }


    public function testHeaders() : void {
        $http = new MockHttpBackend();
        Http::init( $http );
        $panel1 = new SimplePanel();
        $panel1->addHeader( 'X-Foo', 'Bar' );
        $panel2 = new SimplePanel();
        $panel2->addHeader( 'X-Baz: Qux' );
        $page = new PanelPage( [ $panel1, $panel2 ] );
        $page->render();
        $rHeaders = iterator_to_array( $page->getHeaders(), false );
        self::assertCount( 3, $rHeaders );
        self::assertSame( 'Content-Type: text/html; charset=UTF-8', $rHeaders[ 0 ] );
        self::assertSame( 'X-Foo: Bar', $rHeaders[ 1 ] );
        self::assertSame( 'X-Baz: Qux', $rHeaders[ 2 ] );
    }


    public function testPrependPanel() : void {
        $panel1 = new SimplePanel( 'Hello' );
        $panel2 = new SimplePanel( 'World' );
        $page = new PanelPage( [ $panel2 ] );
        $page->prependPanel( $panel1 );
        $st = '<body>HelloWorld</body>';
        self::assertStringContainsString( $st, $page->render() );
    }


    /** @noinspection HtmlUnknownTarget */
    public function testScripts() : void {
        $panel1 = new SimplePanel( 'Hello' );
        $panel1->addScriptUri( '/foo.js' );
        $panel2 = new SimplePanel( 'World' );
        $panel2->addScriptUri( '/bar.js' );
        $page = new PanelPage( [ $panel1, $panel2 ] );
        $st = $page->render();
        self::assertStringContainsString( '<script src="/foo.js"></script>', $st );
        self::assertStringContainsString( '<script src="/bar.js"></script>', $st );

        # Test that the scripts are not duplicated
        $panel2->addScriptUri( '/foo.js' );
        $st = $page->render();
        $stCheck = 'World<script src="/foo.js"></script><script src="/bar.js"></script>';
        self::assertStringContainsString( $stCheck, $st );
    }


}