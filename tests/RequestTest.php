<?php


declare( strict_types = 1 );


use JDWX\Web\Request;
use PHPUnit\Framework\TestCase;
use Shims\MyRequest;


require_once __DIR__ . '/Shims/MyRequest.php';


class RequestTest extends TestCase {


    public function testCOOKIE() : void {
        /** @phpstan-ignore-next-line */
        $req = Request::synthetic( [], [], [ 'foo' => 'bar', 1 => 'baz' ], [], 'GET', '/' );
        static::assertSame( 'bar', $req->COOKIE( 'foo' )->asString() );
        static::assertSame( 'baz', $req->COOKIE( '1' )->asString() );
        static::assertNull( $req->COOKIE( 'bar' ) );
    }


    public function testCookieEx() : void {
        $req = Request::synthetic( [], [], [ 'foo' => 'bar' ], [], 'GET', '/' );
        static::assertSame( 'bar', $req->cookieEx( 'foo' )->asString() );
        static::expectException( OutOfBoundsException::class );
        $req->cookieEx( 'bar' );
    }


    public function testCookieHas() : void {
        $req = Request::synthetic( [], [], [ 'foo' => 'bar' ], [], 'GET', '/' );
        static::assertTrue( $req->cookieHas( 'foo' ) );
        static::assertFalse( $req->cookieHas( 'bar' ) );
        static::assertFalse( $req->cookieHas( 'foo', 'bar' ) );
    }


    public function testFILES() : void {
        $req = Request::synthetic( [], [], [], [ 'foo' => [ 'name' => 'bar' ] ], 'GET', '/' );
        static::assertTrue( $req->FILES()->has( 'foo' ) );
    }


    public function testGET() : void {
        /** @phpstan-ignore-next-line */
        $req = Request::synthetic( [ 'foo' => 'bar', 1 => 'baz' ], [], [], [], 'GET', '/' );
        static::assertSame( 'bar', $req->GET( 'foo' )->asString() );
        static::assertSame( 'baz', $req->GET( '1' )->asString() );
        static::assertNull( $req->GET( 'bar' ) );
    }


    public function testGetEx() : void {
        $req = Request::synthetic( [ 'foo' => 'bar' ], [], [], [], 'GET', '/' );
        static::assertSame( 'bar', $req->getEx( 'foo' )->asString() );
        static::expectException( OutOfBoundsException::class );
        $req->getEx( 'bar' );
    }


    public function testGetGlobal() : void {
        $_SERVER[ 'REQUEST_METHOD' ] = 'GET';
        $_SERVER[ 'REQUEST_URI' ] = '/';
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


    public function testMethodForGlobal() : void {
        MyRequest::whackGlobal();
        $_SERVER[ 'REQUEST_METHOD' ] = 'TEST_METHOD';
        $req = Request::getGlobal();
        static::assertSame( 'TEST_METHOD', $req->method() );
    }


    public function testMethodForManual() : void {
        $req = Request::synthetic( [], [], [], [], 'TEST_METHOD', '/' );
        static::assertSame( 'TEST_METHOD', $req->method() );
    }


    public function testPOST() : void {
        /** @phpstan-ignore-next-line */
        $req = Request::synthetic( [], [ 'foo' => 'bar', 1 => 'baz' ], [], [] );
        static::assertSame( 'bar', $req->POST( 'foo' )->asString() );
        static::assertSame( 'baz', $req->POST( '1' )->asString() );
        static::assertNull( $req->POST( 'bar' ) );
    }


    public function testPath() : void {
        $req = Request::synthetic( [], [], [], [], 'GET', '/foo/bar?a=b&c=d' );
        static::assertSame( '/foo/bar', $req->path() );
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


    public function testUri() : void {
        $req = Request::synthetic( [], [], [], [], 'GET', '/foo/bar?a=b&c=d' );
        static::assertSame( '/foo/bar?a=b&c=d', $req->uri() );
    }


    public function testUriForGlobal() : void {
        MyRequest::whackGlobal();
        $_SERVER[ 'REQUEST_URI' ] = '/foo/bar?a=b&c=d';
        $_SERVER[ 'REQUEST_METHOD' ] = 'GET';
        $req = Request::getGlobal();
        static::assertSame( '/foo/bar?a=b&c=d', $req->uri() );
    }


    public function testUriParts() : void {
        $req = Request::synthetic( [], [], [], [], 'GET', '/foo/bar/baz?a=b&c=d' );
        $parts = $req->uriParts();
        static::assertSame( [ 'foo', 'bar' ], $parts->subFolders );
        static::assertSame( 'baz', $parts->nstFile );
        static::assertSame( 'b', $parts[ 'a' ] );
        static::assertSame( 'd', $parts[ 'c' ] );
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
