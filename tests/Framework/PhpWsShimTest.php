<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Framework;


use JDWX\Strict\OK;
use JDWX\Web\Backends\MockServer;
use JDWX\Web\Framework\PhpWsShim;
use JDWX\Web\Request;
use JDWX\Web\RequestInterface;
use JDWX\Web\Tests\Shims\MyAbstractRouter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( PhpWsShim::class )]
final class PhpWsShimTest extends TestCase {


    public function testRunForExactHook() : void {
        $req = $this->newRequest( '/exact' );
        $router = new MyAbstractRouter( i_req: $req );
        $shim = new PhpWsShim( $router );
        $shim->addHook( '/exact', function () {
            echo 'Hello, world!';
        }, true );
        OK::ob_start();
        self::assertTrue( $shim->run() );
        $output = OK::ob_get_clean();
        self::assertSame( 'Hello, world!', $output );
    }


    public function testRunForHook() : void {
        $req = $this->newRequest( '/hook/example' );
        $router = new MyAbstractRouter( i_req: $req );
        $shim = new PhpWsShim( $router );
        $shim->addHook( '/hook/', function () {
            echo 'Hello, world!';
        } );
        OK::ob_start();
        self::assertTrue( $shim->run() );
        $output = OK::ob_get_clean();
        self::assertSame( 'Hello, world!', $output );
    }


    public function testRunForRouter() : void {
        $req = $this->newRequest();
        $router = new MyAbstractRouter( i_req: $req );
        $router->fnRoute = function () {
            echo 'Hello, world!';
        };
        $shim = new PhpWsShim( $router );
        OK::ob_start();
        self::assertTrue( $shim->run() );
        $output = OK::ob_get_clean();
        self::assertSame( 'Hello, world!', $output );
    }


    public function testRunForStatic() : void {
        $req = $this->newRequest( '/example.txt' );
        $router = new MyAbstractRouter( i_req: $req );
        $shim = new PhpWsShim( $router );
        $shim->addStaticUri( '/' );
        OK::ob_start();
        self::assertTrue( $shim->run() );
        $output = OK::ob_get_clean();
        self::assertStringContainsString( 'This is a test.', $output );
    }


    private function newRequest( string $i_stUri = '/' ) : RequestInterface {
        $srv = new MockServer();
        $srv = $srv->withRequestUri( $i_stUri );
        $srv = $srv->withDocumentRoot( __DIR__ . '/../../example/static' );
        return Request::synthetic( [], [], [], [], $srv );
    }


}
