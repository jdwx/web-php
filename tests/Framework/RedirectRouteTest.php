<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Framework;


use JDWX\Web\Framework\RedirectRoute;
use JDWX\Web\Framework\ResponseInterface;
use JDWX\Web\Framework\RouteTestRouter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( RedirectRoute::class )]
final class RedirectRouteTest extends TestCase {


    public function testAllowPathInfo() : void {
        $rtr = new RouteTestRouter();
        $route = RedirectRoute::make( $rtr, '/from', '/to', 301, false );
        self::assertTrue( $route->allowPathInfo() );

        $routeExact = RedirectRoute::make( $rtr, '/from', '/to', 301, true );
        self::assertFalse( $routeExact->allowPathInfo() );
    }


    public function testHandle() : void {
        $rtr = new RouteTestRouter();
        $route = RedirectRoute::make( $rtr, '/from', '/to', 301, true );
        $rsp = $route->handle( '/from', '', [] );
        assert( $rsp instanceof ResponseInterface );
        self::assertSame( 301, $rsp->getStatusCode() );
        self::assertSame( '/to', $rsp->getHeader( 'Location' ) );
        $st = strval( $rsp );
        self::assertStringContainsString( 'href="/to"', $st );
    }


    public function testStatus() : void {
        $rtr = new RouteTestRouter();
        $route = RedirectRoute::make( $rtr, '/from', '/to', 301, true );
        self::assertSame( 301, $route->status() );
    }


    public function testTarget() : void {
        $rtr = new RouteTestRouter();
        $route = RedirectRoute::make( $rtr, '/from', '/to', 301, true );
        self::assertSame( '/to', $route->target( '/from' ) );

        # This actually wouldn't get called since the router wouldn't match it.
        self::assertSame( '/to', $route->target( '/from/extra' ) );

        $route = RedirectRoute::make( $rtr, '/from', '/to', 301, false );
        self::assertSame( '/to/extra', $route->target( '/from/extra' ) );
        self::assertSame( '/to', $route->target( '/from' ) );
        self::assertSame( '/to/', $route->target( '/from/' ) );
    }


}
