<?php


declare( strict_types = 1 );


namespace Framework;


use JDWX\Web\Backends\MockServer;
use JDWX\Web\Framework\Response;
use JDWX\Web\Framework\RouteMatch;
use JDWX\Web\Framework\RouteRouter;
use JDWX\Web\Request;
use JDWX\Web\RequestInterface;
use LogicException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Shims\MyRoute;
use Shims\MyRouteManager;
use Shims\MyRouteRouter;


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
        self::expectException( LogicException::class );
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
            'get' => function () {
                return Response::text( 'Yup.' );
            },
        ] );
        $mgr->routes[ '/foo' ] = [ new RouteMatch( '/foo', $route, '', [] ) ];
        self::assertFalse( $router->route() );
        self::assertTrue( $router->route( '/foo' ) );
    }


    protected function newRequest( string $i_stMethod, string $i_stUri ) : RequestInterface {
        $srv = new MockServer();
        $srv = $srv->withRequestMethod( $i_stMethod )->withRequestUri( $i_stUri );
        return Request::synthetic( [], [], [], [], $srv );
    }


}