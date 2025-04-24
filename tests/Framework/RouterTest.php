<?php


declare( strict_types = 1 );


namespace Framework;


require_once __DIR__ . '/../Shims/MyRoute.php';
require_once __DIR__ . '/../Shims/MyRouter.php';


use InvalidArgumentException;
use JDWX\Log\BufferLogger;
use JDWX\Web\Backends\MockHttpBackend;
use JDWX\Web\Backends\MockServer;
use JDWX\Web\Framework\Exceptions\MethodNotAllowedException;
use JDWX\Web\Framework\Response;
use JDWX\Web\Framework\Router;
use JDWX\Web\Http;
use JDWX\Web\Request;
use JDWX\Web\RequestInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Shims\MyRoute;
use Shims\MyRouter;


#[CoversClass( Router::class )]
final class RouterTest extends TestCase {


    public function testAddRouteForInvalidRoute() : void {
        $router = new MyRouter();
        self::expectException( InvalidArgumentException::class );
        $router->addRoutePub( '/test', 'invalid' );
    }


    public function testAddRouteForSameRouteTwice() : void {
        $router = new MyRouter();
        $route = new MyRoute( $router );
        $router->addRoutePub( '/test', $route );
        self::expectException( InvalidArgumentException::class );
        $router->addRoutePub( '/test', $route );
    }


    public function testAddRouteForWrongClass() : void {
        $router = new MyRouter();
        self::expectException( InvalidArgumentException::class );
        $router->addRoutePub( '/test', self::class );
    }


    public function testRouteForFailure() : void {
        $http = new MockHttpBackend();
        $log = new BufferLogger();
        Http::init( $http );
        $req = $this->newRequest( 'GET', '/test' );
        $router = new MyRouter( $log, i_req: $req );
        $route = new MyRoute( $router, [ 'get' => function () {
            return null;
        } ] );
        $router->addRoutePub( '/test', $route );

        self::assertFalse( $router->route() );
    }


    public function testRouteForLongestPrefix() : void {
        $req = $this->newRequest( 'GET', '/test/this/that/these' );
        $router = new MyRouter( i_req: $req );
        $route = new MyRoute( $router, [ 'get' => function ( $stPath, $stUri ) {
            return Response::text( "{$stPath}:{$stUri}" );
        } ] );
        $router->addRoutePub( '/test', $route );
        $router->addRoutePub( '/test/', $route );
        $router->addRoutePub( '/test/this/that/', $route );
        $router->addRoutePub( '/test/this/', $route );
        $router->addRoutePub( '/test/this/that/the', $route );
        $router->addRoutePub( '/foo/this/that/these/', $route );
        self::assertSame( '/test/this/that/:/these', $router->routeOutput() );
    }


    public function testRouteForPrefix() : void {
        $req = $this->newRequest( 'GET', '/test/this' );
        $router = new MyRouter( i_req: $req );
        $route = new MyRoute( $router, [ 'get' => function ( $stPath, $stUri ) {
            return Response::text( "{$stPath}:{$stUri}" );
        } ] );
        $router->addRoutePub( '/test/', $route );
        self::assertSame( '/test/:/this', $router->routeOutput() );
    }


    public function testRouteForRootNotPrefix() : void {
        $req = $this->newRequest( 'GET', '/nope' );
        $router = new MyRouter( i_req: $req );
        $route = new MyRoute( $router, [ 'get' => function () {
            return Response::text( 'TEST_GET_ROOT' );
        } ] );
        $router->addRoutePub( '/', $route );
        self::assertFalse( $router->routeQuiet() );
    }


    public function testRouteForRootPrefix() : void {
        $req = $this->newRequest( 'POST', '/nope' );
        $router = new MyRouter( i_req: $req );
        $router->setRootIsPrefixPub();
        $route = new MyRoute( $router, [ 'post' => function ( string $stPath, string $stUri ) {
            return Response::text( $stPath . ':' . $stUri );
        } ] );
        $router->addRoutePub( '/', $route );
        self::assertSame( '/:/nope', $router->routeOutput() );
    }


    public function testRouteForStringRoute() : void {
        $http = new MockHttpBackend();
        Http::init( $http );
        $log = new BufferLogger();
        $req = $this->newRequest( 'GET', '/test' );
        $router = new MyRouter( $log, i_req: $req );
        $router->addRoutePub( '/test', MyRoute::class );
        self::expectException( MethodNotAllowedException::class );
        $router->route();
    }


    public function testRouteForSuccess() : void {
        $req = $this->newRequest( 'GET', '/test' );
        $router = new MyRouter( i_req: $req );
        $route = new MyRoute( $router, [ 'get' => function () {
            return Response::text( 'TEST_GET' );
        } ] );
        $router->addRoutePub( '/test', $route );
        self::assertSame( 'TEST_GET', $router->routeOutput() );
    }


    private function newRequest( string $i_stMethod, string $i_stUri ) : RequestInterface {
        $srv = new MockServer();
        $srv = $srv->withRequestMethod( $i_stMethod )->withRequestUri( $i_stUri );
        return Request::synthetic( [], [], [], [], $srv );
    }


}
