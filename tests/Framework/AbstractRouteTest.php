<?php


declare( strict_types = 1 );


namespace Framework;


use JDWX\Web\Backends\MockServer;
use JDWX\Web\Framework\AbstractRoute;
use JDWX\Web\Framework\Exceptions\MethodNotAllowedException;
use JDWX\Web\Framework\Exceptions\NotImplementedException;
use JDWX\Web\Framework\ResponseInterface;
use JDWX\Web\Framework\RouterInterface;
use JDWX\Web\Panels\SimplePanel;
use JDWX\Web\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Shims\MyRoute;
use Shims\MyRouter;


#[CoversClass( AbstractRoute::class )]
final class AbstractRouteTest extends TestCase {


    public function testHandleDELETE() : void {
        $router = $this->newRouter( 'DELETE' );
        $route = new MyRoute( $router );
        self::expectException( MethodNotAllowedException::class );
        $route->handle( '/', '' );
    }


    public function testHandleForInvalidMethod() : void {
        $router = $this->newRouter( 'INVALID' );
        $route = new MyRoute( $router );
        self::expectException( NotImplementedException::class );
        $route->handle( '/', '' );
    }


    public function testHandleGET() : void {
        $router = $this->newRouter( 'GET' );
        $route = new MyRoute( $router );
        self::expectException( MethodNotAllowedException::class );
        $route->handle( '/', '' );
    }


    public function testHandleHEAD() : void {
        $router = $this->newRouter( 'HEAD' );
        $route = new MyRoute( $router );
        self::expectException( MethodNotAllowedException::class );
        $route->handle( '/', '' );
    }


    public function testHandlePATCH() : void {
        $router = $this->newRouter( 'PATCH' );
        $route = new MyRoute( $router );
        self::expectException( MethodNotAllowedException::class );
        $route->handle( '/', '' );
    }


    public function testHandlePOST() : void {
        $router = $this->newRouter( 'POST' );
        $route = new MyRoute( $router );
        self::expectException( MethodNotAllowedException::class );
        $route->handle( '/', '' );
    }


    public function testHandlePUT() : void {
        $router = $this->newRouter( 'PUT' );
        $route = new MyRoute( $router );
        self::expectException( MethodNotAllowedException::class );
        $route->handle( '/', '' );
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


            protected function handleGET( string $i_stUri, string $i_stPath ) : ResponseInterface {
                return $this->respondHtml( 'Hello World' );
            }


        };
        self::assertStringContainsString(
            '<body>Hello World</body>',
            strval( $route->handle( '/', '/' ) )
        );
    }


    public function testRespondJson() : void {
        $srv = new MockServer();
        $srv = $srv->withRequestMethod( 'GET' )->withRequestUri( '/' );
        $req = Request::synthetic( [], [], [], [], $srv );
        $router = new MyRouter( i_req: $req );
        $route = new class( $router ) extends AbstractRoute {


            protected function handleGET( string $i_stUri, string $i_stPath ) : ResponseInterface {
                return $this->respondJson( [ 'key' => 'value' ] );
            }


        };
        self::assertSame(
            '{"key":"value"}',
            trim( strval( $route->handle( '/', '/' ) ) )
        );
    }


    public function testRespondPanel() : void {
        $srv = new MockServer();
        $srv = $srv->withRequestMethod( 'GET' )->withRequestUri( '/' );
        $req = Request::synthetic( [], [], [], [], $srv );
        $router = new MyRouter( i_req: $req );
        $route = new class( $router ) extends AbstractRoute {


            protected function handleGET( string $i_stUri, string $i_stPath ) : ResponseInterface {
                $panel = new SimplePanel( 'Hello World' );
                return $this->respondPanel( $panel );
            }


        };
        self::assertStringContainsString(
            '<body>Hello World</body>',
            strval( $route->handle( '/', '/' ) )
        );
    }


    public function testRespondText() : void {
        $srv = new MockServer();
        $srv = $srv->withRequestMethod( 'GET' )->withRequestUri( '/' );
        $req = Request::synthetic( [], [], [], [], $srv );
        $router = new MyRouter( i_req: $req );
        $route = new class( $router ) extends AbstractRoute {


            protected function handleGET( string $i_stUri, string $i_stPath ) : ResponseInterface {
                return $this->respondText( 'Hello World' );
            }


        };
        self::assertSame( 'Hello World', strval( $route->handle( '/', '/' ) ) );
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
