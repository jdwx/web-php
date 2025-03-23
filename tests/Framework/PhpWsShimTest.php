<?php


declare( strict_types = 1 );


namespace Framework;


use JDWX\Web\Backends\MockServer;
use JDWX\Web\Framework\PhpWsShim;
use JDWX\Web\Request;
use JDWX\Web\RequestInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Shims\MyRouter;


#[CoversClass( PhpWsShim::class )]
final class PhpWsShimTest extends TestCase {


    public function testRunForExactHook() : void {
        $req = $this->newRequest( '/exact' );
        $router = new MyRouter( i_req: $req );
        $shim = new PhpWsShim( $router );
        $shim->addHook( '/exact', function () {
            echo 'Hello, world!';
        }, true );
        ob_start();
        self::assertTrue( $shim->run() );
        $output = ob_get_clean();
        self::assertSame( 'Hello, world!', $output );
    }


    public function testRunForHook() : void {
        $req = $this->newRequest( '/hook/example' );
        $router = new MyRouter( i_req: $req );
        $shim = new PhpWsShim( $router );
        $shim->addHook( '/hook/', function () {
            echo 'Hello, world!';
        } );
        ob_start();
        self::assertTrue( $shim->run() );
        $output = ob_get_clean();
        self::assertSame( 'Hello, world!', $output );
    }


    public function testRunForRouter() : void {
        $req = $this->newRequest();
        $router = new MyRouter( i_req: $req );
        $router->fnRoute = function () {
            echo 'Hello, world!';
        };
        $shim = new PhpWsShim( $router );
        ob_start();
        self::assertTrue( $shim->run() );
        $output = ob_get_clean();
        self::assertSame( 'Hello, world!', $output );
    }


    public function testRunForStatic() : void {
        $req = $this->newRequest( '/example.txt' );
        $router = new MyRouter( i_req: $req );
        $shim = new PhpWsShim( $router );
        $shim->addStaticUri( '/' );
        ob_start();
        self::assertTrue( $shim->run() );
        $output = ob_get_clean();
        self::assertStringContainsString( 'This is a test.', $output );
    }


    private function newRequest( string $i_stUri = '/' ) : RequestInterface {
        $srv = new MockServer( 'GET', $i_stUri );
        $srv->stDocumentRoot = __DIR__ . '/../../example/static';
        return Request::synthetic( [], [], [], [], $srv );
    }


}
