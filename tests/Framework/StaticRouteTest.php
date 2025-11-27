<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Framework;


require_once __DIR__ . '/../Shims/MyRouter.php';


use JDWX\Web\Backends\MockServer;
use JDWX\Web\Framework\ResponseInterface;
use JDWX\Web\Framework\StaticFileRoute;
use JDWX\Web\Request;
use JDWX\Web\Tests\Shims\MyRouter;
use PHPUnit\Framework\TestCase;


/**
 * @covers \JDWX\Web\Framework\StaticFileRoute
 * @covers \JDWX\Web\Framework\AbstractStaticRoute
 */
final class StaticRouteTest extends TestCase {


    public function testMake() : void {
        $req = Request::synthetic( i_server: new MockServer( [ 'REQUEST_METHOD' => 'GET' ] ) );
        $rtr = new MyRouter( i_req: $req );
        $route = StaticFileRoute::make( $rtr, __DIR__ . '/../../example/static/example.txt' );
        $rsp = $route->handle( '/', '', [] );
        assert( $rsp instanceof ResponseInterface );
        $page = $rsp->getPage();
        self::assertSame( 'This is a test.', $page->render() );
        self::assertSame( 'text/plain', $page->getContentType() );
    }


}
