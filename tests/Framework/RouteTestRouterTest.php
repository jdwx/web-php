<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Framework;


use JDWX\Web\Framework\AbstractRoute;
use JDWX\Web\Framework\ResponseInterface;
use JDWX\Web\Framework\RouteTestRouter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


/**
 * You're clever, young man, very clever. But it's tests all the way down!
 */
#[CoversClass( RouteTestRouter::class )]
final class RouteTestRouterTest extends TestCase {


    public function testAssertGET() : void {
        $rtr = new RouteTestRouter();
        $rtr->assertGET();

        $rtr = new RouteTestRouter( 'POST' );
        $this->expectException( \LogicException::class );
        $rtr->assertGET();
    }


    public function testAssertPOST() : void {
        $rtr = new RouteTestRouter( 'POST' );
        $rtr->assertPOST();

        $rtr = new RouteTestRouter();
        $this->expectException( \LogicException::class );
        $rtr->assertPOST();
    }


    public function testGetHttpError() : void {
        $rtr = new RouteTestRouter();
        $error = $rtr->getHttpError();
        self::assertSame( 'Not Found', $error->errorName( 404 ) );
    }


    public function testIdentities() : void {
        $rtr = new RouteTestRouter();
        self::assertSame( $rtr->log, $rtr->logger() );
        self::assertSame( $rtr->req, $rtr->request() );
        self::assertSame( $rtr->srv, $rtr->server() );
    }


    public function testMethodNotAllowed() : void {
        $rtr = new RouteTestRouter( 'POST' );
        $this->expectException( \LogicException::class );
        $rtr->methodNotAllowed();
    }


    public function testTest() : void {
        $rtr = new RouteTestRouter();
        $route = new class( $rtr ) extends AbstractRoute {


            protected function handleGET( string $i_stUri, string $i_stPath, array $i_rUriParameters ) : ResponseInterface {
                return $this->respondText( 'Hello, World!' );
            }


        };
        $rsp = $rtr->test( $route );
        self::assertSame( 'Hello, World!', strval( $rsp ) );
    }


}
