<?php


declare( strict_types = 1 );


namespace Framework;


use JDWX\Web\Framework\RouteMatch;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Shims\MyRoute;
use Shims\MyRouter;


#[CoversClass( RouteMatch::class )]
final class RouteMatchTest extends TestCase {


    public function testIsExact() : void {
        $match = new RouteMatch( '/foo', MyRoute::class, '', [] );
        self::assertTrue( $match->isExact() );

        $match = new RouteMatch( '/foo', MyRoute::class, '/bar', [] );
        self::assertFalse( $match->isExact() );
    }


    public function testRoute() : void {
        $router = new MyRouter();
        $match = new RouteMatch( '/foo', MyRoute::class, '', [] );
        self::assertInstanceOf( MyRoute::class, $match->route( $router ) );

        $route = new MyRoute( $router );
        $match = new RouteMatch( '/foo', $route, '', [] );
        self::assertSame( $route, $match->route( $router ) );
    }


}