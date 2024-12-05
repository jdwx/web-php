<?php


declare( strict_types = 1 );


use JDWX\Web\Request;
use PHPUnit\Framework\TestCase;


require_once __DIR__ . '/MyRequest.php';


class RequestTest extends TestCase {


    public function testCOOKIE() : void {
        $req = Request::synthetic( [], [], [ 'foo' => 'bar', 1 => 'baz' ], [] );
        static::assertSame( 'bar', $req->COOKIE( 'foo' )->asString() );
        static::assertSame( 'baz', $req->COOKIE( '1' )->asString() );
        static::assertNull( $req->COOKIE( 'bar' ) );
    }


    public function testCookieEx() : void {
        $req = Request::synthetic( [], [], [ 'foo' => 'bar' ], [] );
        static::assertSame( 'bar', $req->cookieEx( 'foo' )->asString() );
        static::expectException( OutOfBoundsException::class );
        $req->cookieEx( 'bar' );
    }


    public function testCookieHas() : void {
        $req = Request::synthetic( [], [], [ 'foo' => 'bar' ], [] );
        static::assertTrue( $req->cookieHas( 'foo' ) );
        static::assertFalse( $req->cookieHas( 'bar' ) );
        static::assertFalse( $req->cookieHas( 'foo', 'bar' ) );
    }


    public function testFILES() : void {
        $req = Request::synthetic( [], [], [], [ 'foo' => [ 'name' => 'bar' ] ] );
        static::assertTrue( $req->FILES()->has( 'foo' ) );
    }


    public function testGET() : void {
        $req = Request::synthetic( [ 'foo' => 'bar', 1 => 'baz' ], [], [], [] );
        static::assertSame( 'bar', $req->GET( 'foo' )->asString() );
        static::assertSame( 'baz', $req->GET( '1' )->asString() );
        static::assertNull( $req->GET( 'bar' ) );
    }


    public function testGetEx() : void {
        $req = Request::synthetic( [ 'foo' => 'bar' ], [], [], [] );
        static::assertSame( 'bar', $req->getEx( 'foo' )->asString() );
        static::expectException( OutOfBoundsException::class );
        $req->getEx( 'bar' );
    }


    public function testGetGlobal() : void {
        $x = MyRequest::getGlobal();
        static::assertInstanceOf( Request::class, $x );
        MyRequest::whackGlobal();
    }


    public function testGetHas() : void {
        $req = Request::synthetic( [ 'foo' => 'bar' ], [], [], [] );
        static::assertTrue( $req->getHas( 'foo' ) );
        static::assertFalse( $req->getHas( 'bar' ) );
        static::assertFalse( $req->getHas( 'foo', 'bar' ) );
    }


    public function testInit() : void {

        $req = Request::init( [ 'foo' => 'bar' ], [], [], [] );
        static::assertInstanceOf( Request::class, $req );

        $req = Request::getGlobal();
        static::assertInstanceOf( Request::class, $req );
        static::assertSame( 'bar', $req->GET( 'foo' )->asString() );

        static::expectException( LogicException::class );
        Request::init( [], [], [], [] );
    }


    public function testIsGET() : void {

        $_SERVER[ 'REQUEST_METHOD' ] = 'GET';
        $req = Request::synthetic( [], [], [], [] );
        static::assertTrue( $req->isGET() );

        $_SERVER[ 'REQUEST_METHOD' ] = 'POST';
        $req = Request::synthetic( [], [], [], [] );
        static::assertFalse( $req->isGET() );

    }


    public function testIsPOST() : void {

        $_SERVER[ 'REQUEST_METHOD' ] = 'POST';
        $req = Request::synthetic( [], [], [], [] );
        static::assertTrue( $req->isPOST() );

        $_SERVER[ 'REQUEST_METHOD' ] = 'GET';
        $req = Request::synthetic( [], [], [], [] );
        static::assertFalse( $req->isPOST() );

    }


    public function testPOST() : void {
        $req = Request::synthetic( [], [ 'foo' => 'bar', 1 => 'baz' ], [], [] );
        static::assertSame( 'bar', $req->POST( 'foo' )->asString() );
        static::assertSame( 'baz', $req->POST( '1' )->asString() );
        static::assertNull( $req->POST( 'bar' ) );
    }


    public function testPostEx() : void {
        $req = Request::synthetic( [], [ 'foo' => 'bar' ], [], [] );
        static::assertSame( 'bar', $req->postEx( 'foo' )->asString() );
        static::expectException( OutOfBoundsException::class );
        $req->postEx( 'bar' );
    }


    public function testPostHas() : void {
        $req = Request::synthetic( [], [ 'foo' => 'bar' ], [], [] );
        static::assertTrue( $req->postHas( 'foo' ) );
        static::assertFalse( $req->postHas( 'bar' ) );
        static::assertFalse( $req->postHas( 'foo', 'bar' ) );
    }


    public function testXCOOKIE() : void {
        $req = Request::synthetic( [], [], [ 'foo' => 'bar' ], [] );
        static::assertTrue( $req->_COOKIE()->has( 'foo' ) );
        static::assertFalse( $req->_COOKIE()->has( 'bar' ) );
    }


    public function testXGET() : void {
        $req = Request::synthetic( [ 'foo' => 'bar' ], [], [], [] );
        static::assertTrue( $req->_GET()->has( 'foo' ) );
        static::assertFalse( $req->_GET()->has( 'bar' ) );
    }


    public function testXPOST() : void {
        $req = Request::synthetic( [], [ 'foo' => 'bar' ], [], [] );
        static::assertTrue( $req->_POST()->has( 'foo' ) );
        static::assertFalse( $req->_POST()->has( 'bar' ) );
    }


}
