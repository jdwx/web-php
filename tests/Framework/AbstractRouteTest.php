<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Framework;


use JDWX\Web\Backends\MockServer;
use JDWX\Web\Framework\AbstractRoute;
use JDWX\Web\Framework\Exceptions\MethodNotAllowedException;
use JDWX\Web\Framework\Exceptions\NotImplementedException;
use JDWX\Web\Framework\ResponseInterface;
use JDWX\Web\Framework\RouterInterface;
use JDWX\Web\Request;
use JDWX\Web\Tests\Shims\MyRoute;
use JDWX\Web\Tests\Shims\MyRouter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( AbstractRoute::class )]
final class AbstractRouteTest extends TestCase {


    public function testAllowPathInfo() : void {
        $router = $this->newRouter( 'GET' );
        $route = new MyRoute( $router );
        self::assertFalse( $route->allowPathInfo() );

        $route = new MyRoute( $router, [], true );
        self::assertTrue( $route->allowPathInfo() );
    }


    public function testHandleCONNECT() : void {
        $router = $this->newRouter( 'CONNECT' );
        $route = new MyRoute( $router );
        self::expectException( MethodNotAllowedException::class );
        $route->handle( '/', '', [] );
    }


    public function testHandleDELETE() : void {
        $router = $this->newRouter( 'DELETE' );
        $route = new MyRoute( $router );
        self::expectException( MethodNotAllowedException::class );
        $route->handle( '/', '', [] );
    }


    public function testHandleForInvalidMethod() : void {
        $router = $this->newRouter( 'InVaLiD' );
        $route = new MyRoute( $router );
        self::expectException( NotImplementedException::class );
        $route->handle( '/', '', [] );
    }


    public function testHandleGET() : void {
        $router = $this->newRouter( 'GET' );
        $route = new MyRoute( $router );
        self::expectException( MethodNotAllowedException::class );
        $route->handle( '/', '', [] );
    }


    public function testHandleHEAD() : void {
        $router = $this->newRouter( 'HEAD' );
        $route = new MyRoute( $router );
        self::expectException( MethodNotAllowedException::class );
        $route->handle( '/', '', [] );
    }


    public function testHandleOPTIONS() : void {
        $router = $this->newRouter( 'OPTIONS' );
        $route = new MyRoute( $router );
        self::expectException( MethodNotAllowedException::class );
        $route->handle( '/', '', [] );
    }


    public function testHandlePATCH() : void {
        $router = $this->newRouter( 'PATCH' );
        $route = new MyRoute( $router );
        self::expectException( MethodNotAllowedException::class );
        $route->handle( '/', '', [] );
    }


    public function testHandlePOST() : void {
        $router = $this->newRouter( 'POST' );
        $route = new MyRoute( $router );
        self::expectException( MethodNotAllowedException::class );
        $route->handle( '/', '', [] );
    }


    public function testHandlePOSTForAllowPOST() : void {
        $router = $this->newRouter( 'POST' );
        $route = new class( $router ) extends MyRoute {


            protected const bool ALLOW_POST = true;


            protected function handleGET( string $i_stUri, string $i_stPath,
                                          array  $i_rUriParameters ) : ResponseInterface {
                return $this->respondText( 'GET' );
            }


        };
        self::assertSame( 'GET', strval( $route->handle( '/', '', [] ) ) );
    }


    public function testHandlePUT() : void {
        $router = $this->newRouter( 'PUT' );
        $route = new MyRoute( $router );
        self::expectException( MethodNotAllowedException::class );
        $route->handle( '/', '', [] );
    }


    public function testHandleTRACE() : void {
        $router = $this->newRouter( 'TRACE' );
        $route = new MyRoute( $router );
        self::expectException( MethodNotAllowedException::class );
        $route->handle( '/', '', [] );
    }


    public function testLogger() : void {
        $router = $this->newRouter( 'GET' );
        $route = new MyRoute( $router );
        self::assertSame( $router->logger(), $route->loggerPub() );
    }


    public function testRespondHtml() : void {
        $srv = new MockServer();
        $srv = $srv->withRequestMethod( 'GET' )->withRequestUri( '/' );
        $req = Request::synthetic( [], [], [], [], $srv );
        $router = new MyRouter( i_req: $req );
        $route = new class( $router ) extends AbstractRoute {


            /** @param array<string, string> $i_rUriParameters */
            protected function handleGET( string $i_stUri, string $i_stPath,
                                          array  $i_rUriParameters ) : ResponseInterface {
                return $this->respondHtml( 'Hello World' );
            }


        };
        self::assertStringContainsString(
            '<body>Hello World</body>',
            strval( $route->handle( '/', '/', [] ) )
        );
    }


    public function testRespondJson() : void {
        $srv = new MockServer();
        $srv = $srv->withRequestMethod( 'GET' )->withRequestUri( '/' );
        $req = Request::synthetic( [], [], [], [], $srv );
        $router = new MyRouter( i_req: $req );
        $route = new class( $router ) extends AbstractRoute {


            /** @param array<string, string> $i_rUriParameters */
            protected function handleGET( string $i_stUri, string $i_stPath,
                                          array  $i_rUriParameters ) : ResponseInterface {
                return $this->respondJson( [ 'key' => 'value' ] );
            }


        };
        self::assertSame(
            '{"key":"value"}',
            trim( strval( $route->handle( '/', '/', [] ) ) )
        );
    }


    public function testRespondText() : void {
        $srv = new MockServer();
        $srv = $srv->withRequestMethod( 'GET' )->withRequestUri( '/' );
        $req = Request::synthetic( [], [], [], [], $srv );
        $router = new MyRouter( i_req: $req );
        $route = new class( $router ) extends AbstractRoute {


            /** @param array<string, string> $i_rUriParameters */
            protected function handleGET( string $i_stUri, string $i_stPath,
                                          array  $i_rUriParameters ) : ResponseInterface {
                return $this->respondText( 'Hello World' );
            }


        };
        self::assertSame( 'Hello World', strval( $route->handle( '/', '/', [] ) ) );
    }


    public function testServer() : void {
        $srv = new MockServer();
        $srv = $srv->withRequestUri( '/' );
        $req = Request::synthetic( i_server: $srv );
        $router = new MyRouter( i_req: $req );
        $route = new MyRoute( $router );
        self::assertSame( $srv, $route->serverPub() );
    }


    private function newRouter( string $i_stMethod ) : RouterInterface {
        $srv = new MockServer();
        $srv = $srv->withRequestMethod( $i_stMethod )->withRequestUri( '/' );
        $req = Request::synthetic( [], [], [], [], $srv );
        return new MyRouter( i_req: $req );
    }


}
