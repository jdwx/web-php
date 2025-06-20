<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Framework;


use JDWX\Log\BufferLogger;
use JDWX\Strict\OK;
use JDWX\Web\Backends\MockHttpBackend;
use JDWX\Web\Backends\MockServer;
use JDWX\Web\Framework\AbstractRouter;
use JDWX\Web\Framework\Exceptions\InternalServerException;
use JDWX\Web\Framework\Exceptions\MethodNotAllowedException;
use JDWX\Web\Framework\HttpError;
use JDWX\Web\Framework\Response;
use JDWX\Web\Http;
use JDWX\Web\Pages\SimpleTextPage;
use JDWX\Web\Request;
use JDWX\Web\Tests\Shims\MyAbstractRouter;
use JDWX\Web\Tests\Shims\MyTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Log\LoggerInterface;


require_once __DIR__ . '/../Shims/MyAbstractRouter.php';
require_once __DIR__ . '/../Shims/MyTestCase.php';


#[CoversClass( AbstractRouter::class )]
final class AbstractRouterTest extends MyTestCase {


    public function testAddHeaderForCombined() : void {
        $http = new MockHttpBackend();
        Http::init( $http );
        $req = $this->newRequest();
        $router = new class( i_req: $req ) extends AbstractRouter {


            public function route() : bool {
                $this->addHeader( 'X-Foo: Bar' );
                return $this->respondText( 'OK' );
            }


        };
        OK::ob_start();
        $router->run();
        ob_end_clean();
        self::assertSame( 'Bar', $http->getHeader( 'X-Foo' ) );
    }


    public function testAddHeaderForSeparate() : void {
        $http = new MockHttpBackend();
        Http::init( $http );
        $req = $this->newRequest();
        $router = new class( i_req: $req ) extends AbstractRouter {


            public function route() : bool {
                $this->addHeader( 'X-Foo', 'Bar' );
                return $this->respondText( 'OK' );
            }


        };
        OK::ob_start();
        $router->run();
        ob_end_clean();
        self::assertSame( 'Bar', $http->getHeader( 'X-Foo' ) );
    }


    public function testAssertGETAndPOSTForGET() : void {
        $req = $this->newRequest( 'GET', '/foo/bar' );
        $router = new MyAbstractRouter( i_req: $req );
        $router->assertGET();
        self::expectException( MethodNotAllowedException::class );
        $router->assertPOST();
    }


    public function testAssertGETAndPOSTForPOST() : void {
        $req = $this->newRequest( 'POST', '/foo/bar' );
        $router = new MyAbstractRouter( i_req: $req );
        $router->assertPOST();
        self::expectException( MethodNotAllowedException::class );
        $router->assertGET();
    }


    public function testGET() : void {
        $req = $this->newRequest( 'GET', '/foo/bar' );
        $router = new MyAbstractRouter( i_req: $req );
        $router->response = Response::text( 'FOO' );
        $st = $router->routeOutput( '/foo/bar' );
        self::assertSame( 'FOO', $st );
    }


    public function testGetHttpError() : void {
        $req = $this->newRequest( 'GET', '/foo/bar' );
        $error = new HttpError();
        $router = new MyAbstractRouter( i_error: $error, i_req: $req );
        self::assertSame( $error, $router->getHttpError() );
    }


    public function testHEAD() : void {
        $req = $this->newRequest( 'HEAD', '/foo/bar' );
        $router = new MyAbstractRouter( i_req: $req );
        $router->response = Response::text( 'FOO' );
        $st = $router->routeOutput( '/foo/bar' );
        self::assertSame( '', $st );
    }


    public function testLogger() : void {
        $log = new BufferLogger();
        $router = new MyAbstractRouter( $log );
        self::assertSame( $log, $router->logger() );

        $router = new MyAbstractRouter();
        self::assertInstanceOf( LoggerInterface::class, $router->logger() );
    }


    public function testRequestValues() : void {
        $req = $this->newRequest( 'GET', '/foo/bar?baz=qux' );
        $router = new MyAbstractRouter( i_req: $req );
        $router->save();
        self::assertSame( '/foo/bar', $router->stPathCheck );
        self::assertSame( '/foo/bar?baz=qux', $router->stUriCheck );
        self::assertSame( 'qux', $router->uriPartsCheck[ 'baz' ] );
        self::assertSame( $req, $router->requestCheck );
    }


    public function testRespondHtml() : void {
        $http = new MockHttpBackend();
        Http::init( $http );
        $req = $this->newRequest();
        $router = new class( i_req: $req ) extends AbstractRouter {


            public function route() : bool {
                $this->respondHtml( '<html lang="en"><body>TEST_HTML</body></html>' );
                return true;
            }


        };
        OK::ob_start();
        $router->run();
        $result = OK::ob_get_clean();
        self::assertStringContainsString( 'TEST_HTML', $result );
    }


    public function testRespondJson() : void {
        $http = new MockHttpBackend();
        Http::init( $http );
        $req = $this->newRequest();
        $router = new class( i_req: $req ) extends AbstractRouter {


            public function route() : bool {
                return $this->respondJson( [ 'foo' => 'bar' ] );
            }


        };
        OK::ob_start();
        $router->run();
        $result = OK::ob_get_clean();
        self::assertSame( 200, $http->getResponseCode() );
        self::assertSame( 'application/json', $http->getHeader( 'Content-Type' ) );
        self::assertStringContainsString( '"foo":"bar"', $result );
    }


    public function testRespondPage() : void {
        $http = new MockHttpBackend();
        Http::init( $http );
        $req = $this->newRequest();
        $router = new class( i_req: $req ) extends AbstractRouter {


            public function route() : bool {
                $this->respondPage( new SimpleTextPage( 'TEST_CONTENT' ) );
                return true;
            }


        };
        OK::ob_start();
        $router->run();
        $result = OK::ob_get_clean();
        self::assertSame( 'TEST_CONTENT', $result );
        self::assertSame( 200, $http->getResponseCode() );
        self::assertSame( 'text/plain', $http->getHeader( 'Content-Type' ) );
    }


    public function testRespondText() : void {
        $http = new MockHttpBackend();
        Http::init( $http );
        $req = $this->newRequest();
        $router = new class( i_req: $req ) extends AbstractRouter {


            public function route() : bool {
                $this->respondText( 'TEST_CONTENT' );
                return true;
            }


        };
        OK::ob_start();
        $router->run();
        $result = OK::ob_get_clean();
        self::assertSame( 'TEST_CONTENT', $result );
    }


    public function testRunForInternalServerError() : void {
        $req = $this->newRequest();
        $logger = new BufferLogger();
        $router = new MyAbstractRouter( $logger, i_req: $req );
        $router->fnRoute = function () {
            throw new InternalServerException( 'TEST_EXCEPTION' );
        };
        OK::ob_start();
        $router->run();
        $st = OK::ob_get_clean();
        self::assertStringContainsString( '500', $st );
        self::assertStringContainsString( 'Internal Server Error', $st );
        self::assertSame( 500, Http::getResponseCode() );
        $log = $logger->shiftLogEx();
        self::assertStringContainsString( 'TEST_EXCEPTION', $log->message );
    }


    public function testRunForInvalidUrl() : void {
        $req = $this->newRequest( 'GET', '/test/..' );
        $logger = new BufferLogger();
        $router = new MyAbstractRouter( $logger, i_req: $req );
        OK::ob_start();
        $router->run();
        $st = OK::ob_get_clean();
        self::assertStringContainsString( '400 Bad Request', $st );
    }


    public function testRunForNotFound() : void {
        $req = $this->newRequest( 'GET', '/foo/bar' );
        $logger = new BufferLogger();
        $router = new MyAbstractRouter( $logger, i_req: $req );
        $router->bReturn = false;
        OK::ob_start();
        $router->run();
        $st = OK::ob_get_clean();
        self::assertStringContainsString( '404', $st );
        self::assertStringContainsString( 'Not Found', $st );
        $log = $logger->shiftLogEx();
        self::assertSame( '(nothing)', $log->context[ 'display' ] );
    }


    public function testRunForSuccess() : void {
        $req = $this->newRequest( 'GET', '/foo/bar' );
        $router = new MyAbstractRouter( i_req: $req );
        $router->fnRoute = function () {
            echo 'foo';
        };
        OK::ob_start();
        $router->run();
        $result = OK::ob_get_clean();
        self::assertSame( 'foo', $result );
    }


    public function testServer() : void {
        $srv = new MockServer();
        $req = Request::synthetic( i_server: $srv );
        $router = new MyAbstractRouter( i_req: $req );
        self::assertSame( $srv, $router->server() );
    }


    private function newRequest( string $i_stMethod = 'GET', string $i_stURI = '/' ) : Request {
        $srv = new MockServer();
        $srv = $srv->withRequestMethod( $i_stMethod )->withRequestUri( $i_stURI );
        return Request::synthetic( i_server: $srv );
    }


}
