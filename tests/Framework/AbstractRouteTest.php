<?php


declare( strict_types = 1 );


namespace Framework;


use JDWX\Web\Backends\MockServer;
use JDWX\Web\Framework\AbstractRoute;
use JDWX\Web\Framework\Exceptions\MethodNotAllowedException;
use JDWX\Web\Framework\Exceptions\NotImplementedException;
use JDWX\Web\Framework\RouterInterface;
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


    private function newRouter( string $i_stMethod ) : RouterInterface {
        $srv = new MockServer( $i_stMethod, '/' );
        $req = Request::synthetic( [], [], [], [], $srv );
        return new MyRouter( i_req: $req );
    }


}
