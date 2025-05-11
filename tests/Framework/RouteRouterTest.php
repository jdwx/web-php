<?php


declare( strict_types = 1 );


namespace Framework;


use JDWX\Web\Backends\MockServer;
use JDWX\Web\Framework\RouteMatch;
use JDWX\Web\Framework\RouteRouter;
use JDWX\Web\Request;
use JDWX\Web\RequestInterface;
use LogicException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Shims\MyRoute;
use Shims\MyRouteManager;


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


    protected function newRequest( string $i_stMethod, string $i_stUri ) : RequestInterface {
        $srv = new MockServer();
        $srv = $srv->withRequestMethod( $i_stMethod )->withRequestUri( $i_stUri );
        return Request::synthetic( [], [], [], [], $srv );
    }


}