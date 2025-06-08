<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Shims;


use InvalidArgumentException;
use JDWX\Web\Backends\MockHttpBackend;
use JDWX\Web\Backends\MockServer;
use JDWX\Web\Framework\Exceptions\MethodNotAllowedException;
use JDWX\Web\Framework\Response;
use JDWX\Web\Http;
use JDWX\Web\Request;
use JDWX\Web\RequestInterface;
use PHPUnit\Framework\TestCase;


abstract class MyRouteRouterTestBase extends TestCase {


    public function testAddRouteForInvalidRoute() : void {
        $router = $this->newRouter();
        self::expectException( InvalidArgumentException::class );
        $router->addRoutePub( '/test', 'invalid' );
    }


    public function testAddRouteForSameRouteTwice() : void {
        $router = $this->newRouter();
        $route = new MyRoute( $router );
        $router->addRoutePub( '/test', $route );
        self::expectException( InvalidArgumentException::class );
        $router->addRoutePub( '/test', $route );
    }


    public function testAddRouteForWrongClass() : void {
        $router = $this->newRouter();
        self::expectException( InvalidArgumentException::class );
        $router->addRoutePub( '/test', self::class );
    }


    public function testRouteForFailure() : void {
        $http = new MockHttpBackend();
        Http::init( $http );
        $req = $this->newRequest( 'GET', '/test' );
        $router = $this->newRouter( i_req: $req );
        $route = new MyRoute( $router, [
            'GET' => function () {
                return null;
            },
        ] );
        $router->addRoutePub( '/test', $route );

        self::assertFalse( $router->route() );
    }


    public function testRouteForFragment() : void {
        $req = $this->newRequest( 'GET', '/test#foo' );
        $router = $this->newRouter( i_req: $req );
        $route = new MyRoute( $router, [
            'GET' => function ( $stUri, $stPath ) {
                return Response::text( "{$stUri}:{$stPath}" );
            },
        ] );
        $router->addRoutePub( '/test', $route );
        self::assertSame( '/test:', $router->routeOutput() );
    }


    public function testRouteForLongestPrefix() : void {
        $req = $this->newRequest( 'GET', '/test/this/that/these' );
        $router = $this->newRouter( i_req: $req );
        $route = new MyRoute( $router, [
            'GET' => function ( $stPath, $stUri ) {
                return Response::text( "{$stPath}:{$stUri}" );
            },
        ], true );
        $router->addRoutePub( '/test', $route );
        $router->addRoutePub( '/test/', $route );
        $router->addRoutePub( '/test/this/that/', $route );
        $router->addRoutePub( '/test/this/', $route );
        $router->addRoutePub( '/test/this/that/the', $route );
        $router->addRoutePub( '/foo/this/that/these/', $route );
        self::assertSame( '/test/this/that/:/these', $router->routeOutput(), $router::class );
    }


    public function testRouteForPathInfoNotAllowed() : void {
        $req = $this->newRequest( 'GET', '/test/' );
        $router = $this->newRouter( i_req: $req );
        $route = new MyRoute( $router, [
            'GET' => function () {
                return Response::text( 'TEST_GET' );
            },
        ] );
        $router->addRoutePub( '/test/', $route );
        self::assertSame( 'TEST_GET', $router->routeOutput() );

        $req = $this->newRequest( 'GET', '/test/this' );
        $router = $this->newRouter( i_req: $req );
        $router->addRoutePub( '/test/', $route );
        self::assertFalse( $router->routeQuiet() );
    }


    public function testRouteForPrefix() : void {
        $req = $this->newRequest( 'GET', '/test/this' );
        $router = $this->newRouter( i_req: $req );
        $route = new MyRoute( $router, [
            'GET' => function ( $stPath, $stUri ) {
                return Response::text( "{$stPath}:{$stUri}" );
            },
        ], true );
        $router->addRoutePub( '/test/', $route );
        self::assertSame( '/test/:/this', $router->routeOutput() );
    }


    public function testRouteForQueryParameters() : void {
        $req = $this->newRequest( 'GET', '/test?foo=bar' );
        $router = $this->newRouter( i_req: $req );
        $route = new MyRoute( $router, [
            'GET' => function ( $stUri, $stPath ) {
                return Response::text( "{$stUri}:{$stPath}" );
            },
        ], true );
        $router->addRoutePub( '/test', $route );
        self::assertSame( '/test:', $router->routeOutput() );
    }


    public function testRouteForRootNotPrefix() : void {
        $req = $this->newRequest( 'GET', '/nope' );
        $router = $this->newRouter( i_req: $req );
        $route = new MyRoute( $router, [
            'GET' => function () {
                return Response::text( 'TEST_GET_ROOT' );
            },
        ] );
        $router->addRoutePub( '/', $route );
        self::assertFalse( $router->routeQuiet() );
    }


    public function testRouteForRootPrefix() : void {
        $req = $this->newRequest( 'POST', '/nope' );
        $router = $this->newRouter( i_req: $req );
        $router->setRootIsPrefixPub();
        $route = new MyRoute( $router, [
            'POST' => function ( string $stPath, string $stUri ) {
                return Response::text( $stPath . ':' . $stUri );
            },
        ], true );
        $router->addRoutePub( '/', $route );
        self::assertSame( '/:/nope', $router->routeOutput() );
    }


    public function testRouteForStringRoute() : void {
        $http = new MockHttpBackend();
        Http::init( $http );
        $req = $this->newRequest( 'GET', '/test' );
        $router = $this->newRouter( i_req: $req );
        $router->addRoutePub( '/test', MyRoute::class );
        self::expectException( MethodNotAllowedException::class );
        $router->route();
    }


    public function testRouteForSuccess() : void {
        $req = $this->newRequest( 'GET', '/test' );
        $router = $this->newRouter( $req );
        $route = new MyRoute( $router, [
            'GET' => function () {
                return Response::text( 'TEST_GET' );
            },
        ] );
        $router->addRoutePub( '/test', $route );
        self::assertSame( 'TEST_GET', $router->routeOutput() );
    }


    protected function newRequest( string $i_stMethod, string $i_stUri ) : RequestInterface {
        $srv = new MockServer();
        $srv = $srv->withRequestMethod( $i_stMethod )->withRequestUri( $i_stUri );
        return Request::synthetic( [], [], [], [], $srv );
    }


    abstract protected function newRouter( ?RequestInterface $i_req = null ) : MyRouterInterface;


}