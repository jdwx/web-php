<?php


declare( strict_types = 1 );


use JDWX\Log\BufferLogger;
use JDWX\Web\Framework\Exceptions\InternalServerException;
use JDWX\Web\Framework\Exceptions\MethodNotAllowed;
use JDWX\Web\Http;
use JDWX\Web\Request;
use Shims\MyRouter;
use Shims\MyTestCase;


require_once __DIR__ . '/Shims/MyRouter.php';
require_once __DIR__ . '/Shims/MyTestCase.php';


final class RouterTest extends MyTestCase {


    public function testAssertGETAndPOSTForGET() : void {
        $req = Request::synthetic( i_nstMethod: 'GET', i_nstUri: '/foo/bar' );
        $router = new MyRouter( i_req: $req );
        $router->assertGET();
        self::expectException( MethodNotAllowed::class );
        $router->assertPOST();
    }


    public function testAssertGETAndPOSTForPOST() : void {
        $req = Request::synthetic( i_nstMethod: 'POST', i_nstUri: '/foo/bar' );
        $router = new MyRouter( i_req: $req );
        $router->assertPOST();
        self::expectException( MethodNotAllowed::class );
        $router->assertGET();
    }


    public function testRequestValues() : void {
        $req = Request::synthetic( i_nstMethod: 'GET', i_nstUri: '/foo/bar?baz=qux' );
        $router = new MyRouter( i_req: $req );
        $router->save();
        self::assertSame( '/foo/bar', $router->stPathCheck );
        self::assertSame( '/foo/bar?baz=qux', $router->stUriCheck );
        self::assertSame( 'qux', $router->uriPartsCheck[ 'baz' ] );
        self::assertSame( $req, $router->requestCheck );
    }


    public function testRunForInternalServerError() : void {
        $req = Request::synthetic( i_nstMethod: 'GET', i_nstUri: '/foo/bar' );
        $logger = new BufferLogger();
        $router = new MyRouter( $logger, i_req: $req );
        $router->fnRoute = function () {
            throw new InternalServerException( 'TEST_EXCEPTION' );
        };
        ob_start();
        $router->run();
        $st = ob_get_clean();
        self::assertStringContainsString( '500', $st );
        self::assertStringContainsString( 'Internal Server Error', $st );
        self::assertSame( 500, Http::getResponseCode() );
        $log = $logger->shiftLog();
        self::assertStringContainsString( 'TEST_EXCEPTION', $log->message );
    }


    public function testRunForNotFound() : void {
        $req = Request::synthetic( i_nstMethod: 'GET', i_nstUri: '/foo/bar' );
        $logger = new BufferLogger();
        $router = new MyRouter( $logger, i_req: $req );
        $router->bReturn = false;
        ob_start();
        $router->run();
        $st = ob_get_clean();
        self::assertStringContainsString( '404', $st );
        self::assertStringContainsString( 'Not Found', $st );
        $log = $logger->shiftLog();
        self::assertSame( '(nothing)', $log->context[ 'display' ] );
    }


    public function testRunForSuccess() : void {
        $req = Request::synthetic( i_nstMethod: 'GET', i_nstUri: '/foo/bar' );
        $router = new MyRouter( i_req: $req );
        $router->fnRoute = function () {
            echo 'foo';
        };
        ob_start();
        $router->run();
        $result = ob_get_clean();
        self::assertSame( 'foo', $result );
    }


}
