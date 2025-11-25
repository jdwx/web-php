<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Framework;


use JDWX\Web\Backends\MockHttpBackend;
use JDWX\Web\Backends\MockServer;
use JDWX\Web\Framework\MapRouteManager;
use JDWX\Web\Framework\Response;
use JDWX\Web\Framework\RouteMatch;
use JDWX\Web\Framework\RouteRouter;
use JDWX\Web\Http;
use JDWX\Web\Request;
use JDWX\Web\RequestInterface;
use JDWX\Web\Tests\Shims\MyRoute;
use JDWX\Web\Tests\Shims\MyRouteManager;
use JDWX\Web\Tests\Shims\MyRouteRouter;
use LogicException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


require_once __DIR__ . '/../Shims/MyRoute.php';
require_once __DIR__ . '/../Shims/MyRouteManager.php';
require_once __DIR__ . '/../Shims/MyRouteRouter.php';


#[CoversClass( RouteRouter::class )]
final class RouteRouterTest extends TestCase {


    public function testRouteForAmbiguousPrefix() : void {
        $req = $this->newRequest( 'GET', '/foo/bar' );
        $mgr = new MyRouteManager();
        $mgr->routes[ '/foo/bar' ] = [
            new RouteMatch( '/foo/bar', MyRoute::class, '', [] ),
            new RouteMatch( '/foo/bar', MyRoute::class, '', [] ),
        ];
        $router = new RouteRouter( $mgr, i_req: $req );
        $this->expectException( LogicException::class );
        $router->route();
    }


    public function testRouteForBadPrefix() : void {
        $req = $this->newRequest( 'GET', '/foobar' );
        $mgr = new MyRouteManager();
        $mgr->routes[ '/foobar' ] = new RouteMatch( '/foo', MyRoute::class, 'bar', [] );
        $router = new RouteRouter( $mgr, i_req: $req );
        self::assertFalse( $router->route() );
    }


    public function testRouteForOverride() : void {
        $req = $this->newRequest( 'GET', '/foo/bar' );
        $mgr = new MyRouteManager();
        $router = new MyRouteRouter( $mgr, i_req: $req );
        $route = new MyRoute( $router, [
            'GET' => function () {
                return Response::text( 'Yup.' );
            },
        ] );
        $mgr->routes[ '/foo' ] = [ new RouteMatch( '/foo', $route, '', [] ) ];
        ob_start();
        self::assertFalse( $router->route() );
        self::assertTrue( $router->route( '/foo' ) );
        ob_end_clean();
    }


    public function testRouteForRedirectWithExact() : void {
        $req = $this->newRequest( 'GET', '/foo/bar' );
        $mgr = new MapRouteManager();
        $router = new MyRouteRouter( $mgr, i_req: $req );
        $router->addRedirectPub( '/foo/', '/baz/' );
        ob_start();
        $x = $router->route();
        $st = ob_get_clean();
        self::assertFalse( $x );
        self::assertSame( '', $st );

        $http = new MockHttpBackend();
        Http::init( $http );

        ob_start();
        $x = $router->route( '/foo/' );
        $st = ob_get_clean();
        self::assertTrue( $x );
        assert( is_string( $st ) );
        self::assertStringContainsString( 'href="/baz/"', $st );
        self::assertSame( 301, $http->getResponseCode() );
        self::assertSame( '/baz/', $http->getHeader( 'Location' ) );
    }


    public function testRouteForRedirectWithNotExact() : void {
        $req = $this->newRequest( 'GET', '/foo/bar' );
        $mgr = new MapRouteManager();
        $router = new MyRouteRouter( $mgr, i_req: $req );

        $http = new MockHttpBackend();
        Http::init( $http );

        $router->addRedirectPub( '/foo/', '/baz/', 302, false );
        ob_start();
        $x = $router->route();
        $st = ob_get_clean();
        self::assertTrue( $x );
        assert( is_string( $st ) );
        self::assertStringContainsString( 'href="/baz/"', $st );
        self::assertSame( 302, $http->getResponseCode() );
        self::assertSame( '/baz/', $http->getHeader( 'Location' ) );
    }


    public function testRouteForStaticRoute() : void {
        $req = $this->newRequest( 'GET', '/foo' );
        $mgr = new MapRouteManager();
        $router = new MyRouteRouter( $mgr, i_req: $req );

        $http = new MockHttpBackend();
        Http::init( $http );

        $router->addStaticRoute( '/foo', __DIR__ . '/../../example/static/example.txt' );
        ob_start();
        $x = $router->route();

        $st = ob_get_clean();
        self::assertTrue( $x );
        assert( is_string( $st ) );
        self::assertSame( 'This is a test.', $st );

    }


    protected function newRequest( string $i_stMethod, string $i_stUri ) : RequestInterface {
        $srv = new MockServer();
        $srv = $srv->withRequestMethod( $i_stMethod )->withRequestUri( $i_stUri );
        return Request::synthetic( [], [], [], [], $srv );
    }


}