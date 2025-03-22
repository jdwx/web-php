<?php


declare( strict_types = 1 );


use JDWX\Web\Backends\MockServer;
use JDWX\Web\Request;
use JDWX\Web\RequestInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Shims\MyRequest;


require_once __DIR__ . '/Shims/MyRequest.php';


#[CoversClass( Request::class )]
final class RequestTest extends TestCase {


    public function testGetGlobal() : void {
        $x = $this->newGlobalRequest();
        self::assertInstanceOf( Request::class, $x );
    }


    public function testGlobalCookie() : void {
        $req = $this->newGlobalRequest( i_rCookie: [ 'foo' => 'bar' ] );
        self::assertSame( 'bar', $req->COOKIE( 'foo' )->asString() );
    }


    public function testGlobalFiles() : void {
        $req = $this->newGlobalRequest( i_rFiles: [ 'foo' => [ 'name' => 'bar' ] ] );
        self::assertTrue( $req->FILES()->has( 'foo' ) );
    }


    public function testGlobalGet() : void {
        $req = $this->newGlobalRequest( [ 'foo' => 'bar' ] );
        self::assertSame( 'bar', $req->GET( 'foo' )->asString() );
    }


    public function testGlobalMethod() : void {
        $req = $this->newGlobalRequest( i_stMethod: 'TEST_METHOD' );
        self::assertSame( 'TEST_METHOD', $req->method() );
    }


    public function testGlobalPost() : void {
        $req = $this->newGlobalRequest( i_rPost: [ 'foo' => 'bar' ] );
        self::assertSame( 'bar', $req->POST( 'foo' )->asString() );
    }


    public function testGlobalUri() : void {
        $req = $this->newGlobalRequest( i_stURI: '/foo/bar?a=b&c=d' );
        self::assertSame( '/foo/bar?a=b&c=d', $req->uri() );
    }


    public function testInit() : void {
        MyRequest::whackGlobal();

        $srv = new MockServer();
        $req = Request::init( [ 'foo' => 'bar' ], [], [], [], $srv );
        self::assertInstanceOf( Request::class, $req );

        $req = Request::getGlobal();
        self::assertInstanceOf( Request::class, $req );
        self::assertSame( 'bar', $req->GET( 'foo' )->asString() );

        self::expectException( LogicException::class );
        Request::init( [], [], [], [], $srv );
    }


    public function testMethodForGlobal() : void {
        MyRequest::whackGlobal();
        $_SERVER[ 'REQUEST_METHOD' ] = 'TEST_METHOD';
        $req = Request::getGlobal();
        self::assertSame( 'TEST_METHOD', $req->method() );
    }


    public function testUriForGlobal() : void {
        MyRequest::whackGlobal();
        $_SERVER[ 'REQUEST_URI' ] = '/foo/bar?a=b&c=d';
        $_SERVER[ 'REQUEST_METHOD' ] = 'GET';
        $req = Request::getGlobal();
        self::assertSame( '/foo/bar?a=b&c=d', $req->uri() );
    }


    /**
     * @param array<string, string|list<string>> $i_rGet
     * @param array<string, string|list<string>> $i_rPost
     * @param array<string, string> $i_rCookie
     * @param mixed[] $i_rFiles
     */
    private function newGlobalRequest( array  $i_rGet = [], array $i_rPost = [],
                                       array  $i_rCookie = [], array $i_rFiles = [],
                                       string $i_stMethod = 'GET', string $i_stURI = '/' ) : RequestInterface {
        MyRequest::whackGlobal();
        foreach ( array_keys( $_GET ) as $k ) {
            unset( $_GET[ $k ] );
        }
        foreach ( $i_rGet as $k => $v ) {
            $_GET[ $k ] = $v;
        }
        foreach ( array_keys( $_POST ) as $k ) {
            unset( $_POST[ $k ] );
        }
        foreach ( $i_rPost as $k => $v ) {
            $_POST[ $k ] = $v;
        }
        foreach ( array_keys( $_COOKIE ) as $k ) {
            unset( $_COOKIE[ $k ] );
        }
        foreach ( $i_rCookie as $k => $v ) {
            $_COOKIE[ $k ] = $v;
        }
        foreach ( array_keys( $_FILES ) as $k ) {
            unset( $_FILES[ $k ] );
        }
        foreach ( $i_rFiles as $k => $v ) {
            $_FILES[ $k ] = $v;
        }
        $_SERVER[ 'REQUEST_METHOD' ] = $i_stMethod;
        $_SERVER[ 'REQUEST_URI' ] = $i_stURI;
        return Request::getGlobal();
    }


}
