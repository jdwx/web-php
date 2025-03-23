<?php


declare( strict_types = 1 );


use JDWX\Log\BufferLogger;
use JDWX\Web\Backends\MockHttpBackend;
use JDWX\Web\Backends\MockServer;
use JDWX\Web\Framework\AbstractRouter;
use JDWX\Web\Framework\Exceptions\InternalServerException;
use JDWX\Web\Framework\Exceptions\MethodNotAllowedException;
use JDWX\Web\Framework\HttpError;
use JDWX\Web\Http;
use JDWX\Web\Request;
use JDWX\Web\TextPage;
use PHPUnit\Framework\Attributes\CoversClass;
use Shims\MyRouter;
use Shims\MyTestCase;


require_once __DIR__ . '/Shims/MyRouter.php';
require_once __DIR__ . '/Shims/MyTestCase.php';


#[CoversClass( AbstractRouter::class )]
final class AbstractRouterTest extends MyTestCase {


    public function testAssertGETAndPOSTForGET() : void {
        $req = $this->newRequest( 'GET', '/foo/bar' );
        $router = new MyRouter( i_req: $req );
        $router->assertGET();
        self::expectException( MethodNotAllowedException::class );
        $router->assertPOST();
    }


    public function testAssertGETAndPOSTForPOST() : void {
        $req = $this->newRequest( 'POST', '/foo/bar' );
        $router = new MyRouter( i_req: $req );
        $router->assertPOST();
        self::expectException( MethodNotAllowedException::class );
        $router->assertGET();
    }


    public function testGetHttpError() : void {
        $req = $this->newRequest( 'GET', '/foo/bar' );
        $error = new HttpError();
        $router = new MyRouter( i_error: $error, i_req: $req );
        self::assertSame( $error, $router->getHttpError() );
    }


    public function testRequestValues() : void {
        $req = $this->newRequest( 'GET', '/foo/bar?baz=qux' );
        $router = new MyRouter( i_req: $req );
        $router->save();
        self::assertSame( '/foo/bar', $router->stPathCheck );
        self::assertSame( '/foo/bar?baz=qux', $router->stUriCheck );
        self::assertSame( 'qux', $router->uriPartsCheck[ 'baz' ] );
        self::assertSame( $req, $router->requestCheck );
    }


    public function testRespond() : void {
        $http = new MockHttpBackend();
        Http::init( $http );
        $req = $this->newRequest();
        $router = new class( i_req: $req ) extends AbstractRouter {
            public function route() : bool {
                $this->respond( new TextPage( 'TEST_CONTENT' ) );
                return true;
            }
        };
        ob_start();
        $router->run();
        $result = ob_get_clean();
        self::assertSame( 'TEST_CONTENT', $result );
        self::assertSame( 200, $http->getResponseCode() );
        self::assertSame( 'text/plain', $http->getHeader( 'Content-Type' ) );
    }


    public function testRespondJson() : void {
        $http = new MockHttpBackend();
        Http::init( $http );
        $req = $this->newRequest();
        $router = new class(i_req: $req) extends AbstractRouter {
            public function route() : bool {
                return $this->respondJson( [ 'foo' => 'bar' ] );
            }
        };
        ob_start();
        $router->run();
        $result = ob_get_clean();
        self::assertSame( 200, $http->getResponseCode() );
        self::assertSame( 'application/json', $http->getHeader( 'Content-Type' ) );
        self::assertStringContainsString( '"foo":"bar"', $result );
    }


    public function testRunForInternalServerError() : void {
        $req = $this->newRequest();
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
        $req = $this->newRequest( 'GET', '/foo/bar' );
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
        $req = $this->newRequest( 'GET', '/foo/bar' );
        $router = new MyRouter( i_req: $req );
        $router->fnRoute = function () {
            echo 'foo';
        };
        ob_start();
        $router->run();
        $result = ob_get_clean();
        self::assertSame( 'foo', $result );
    }


    private function newRequest( string $i_stMethod = 'GET', string $i_stURI = '/' ) : Request {
        $srv = new MockServer( $i_stMethod, $i_stURI );
        return Request::synthetic( i_server: $srv );
    }


}
