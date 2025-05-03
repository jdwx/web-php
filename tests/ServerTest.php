<?php


declare( strict_types = 1 );


use JDWX\Web\Server;
use PHPUnit\Framework\TestCase;


class ServerTest extends TestCase {


    public function testDocumentRoot() : void {
        $srv = new Server( [
            'DOCUMENT_ROOT' => 'foo',
        ] );
        self::assertSame( 'foo', $srv->documentRoot() );

        $srv = $srv->withDocumentRoot( 'bar' );
        self::assertSame( 'bar', $srv->documentRoot() );
    }


    public function testHttpHost() : void {
        $srv = new Server( [
            'HTTP_HOST' => 'foo',
        ] );
        self::assertSame( 'foo', $srv->httpHost() );

        $srv = $srv->withHttpHost( 'bar' );
        self::assertSame( 'bar', $srv->httpHost() );

        $srv = $srv->withHttpHost( null );
        self::assertNull( $srv->httpHost() );
        self::assertSame( 'baz', $srv->httpHost( 'baz' ) );
    }


    public function testHttpHostEx() : void {
        $srv = new Server( [
            'HTTP_HOST' => 'foo',
        ] );
        self::assertSame( 'foo', $srv->httpHostEx() );

        $srv = $srv->withHttpHost( null );
        self::assertSame( 'bar', $srv->httpHostEx( 'bar' ) );
        self::expectException( RuntimeException::class );
        $srv->httpHostEx();
    }


    public function testHttpReferer() : void {
        $srv = new Server( [
            'HTTP_REFERER' => 'foo',
        ] );
        self::assertSame( 'foo', $srv->httpReferer() );

        $srv = $srv->withHttpReferer( 'bar' );
        self::assertSame( 'bar', $srv->httpReferer() );

        $srv = $srv->withHttpReferer( null );
        self::assertNull( $srv->httpReferer() );
        self::assertSame( 'baz', $srv->httpReferer( 'baz' ) );
    }


    public function testHttpRefererEx() : void {
        $srv = new Server( [
            'HTTP_REFERER' => 'foo',
        ] );
        self::assertSame( 'foo', $srv->httpRefererEx() );

        $srv = $srv->withHttpReferer( null );
        self::assertSame( 'bar', $srv->httpRefererEx( 'bar' ) );
        self::expectException( RuntimeException::class );
        $srv->httpRefererEx();
    }


    public function testHttpUserAgent() : void {
        $srv = new Server( [
            'HTTP_USER_AGENT' => 'foo',
        ] );
        self::assertSame( 'foo', $srv->httpUserAgent() );

        $srv = $srv->withHttpUserAgent( 'bar' );
        self::assertSame( 'bar', $srv->httpUserAgent() );

        $srv = $srv->withHttpUserAgent( null );
        self::assertNull( $srv->httpUserAgent() );
        self::assertSame( 'baz', $srv->httpUserAgent( 'baz' ) );
    }


    public function testHttpUserAgentEx() : void {
        $srv = new Server( [
            'HTTP_USER_AGENT' => 'foo',
        ] );
        self::assertSame( 'foo', $srv->httpUserAgentEx() );

        $srv = $srv->withHttpUserAgent( null );
        self::assertSame( 'bar', $srv->httpUserAgentEx( 'bar' ) );
        self::expectException( RuntimeException::class );
        $srv->httpUserAgentEx();
    }


    public function testHttps() : void {
        $srv = new Server( [
            'HTTPS' => 'on',
        ] );
        self::assertTrue( $srv->https() );

        $srv = $srv->withHttps( false );
        self::assertFalse( $srv->https() );
    }


    public function testPathInfo() : void {
        $srv = new Server( [
            'PATH_INFO' => 'foo',
        ] );
        self::assertSame( 'foo', $srv->pathInfo() );

        $srv = $srv->withPathInfo( 'bar' );
        self::assertSame( 'bar', $srv->pathInfo() );
    }


    public function testPhpSelf() : void {
        $srv = new Server( [
            'PHP_SELF' => 'foo',
        ] );
        self::assertSame( 'foo', $srv->phpSelf() );

        $srv = $srv->withPhpSelf( 'bar' );
        self::assertSame( 'bar', $srv->phpSelf() );
    }


    public function testRemoteAddr() : void {
        $srv = new Server( [
            'REMOTE_ADDR' => 'foo',
        ] );
        self::assertSame( 'foo', $srv->remoteAddr() );

        $srv = $srv->withRemoteAddr( 'bar' );
        self::assertSame( 'bar', $srv->remoteAddr() );
    }


    public function testRemotePort() : void {
        $srv = new Server( [
            'REMOTE_PORT' => '123',
        ] );
        self::assertSame( 123, $srv->remotePort() );

        $srv = $srv->withRemotePort( 456 );
        self::assertSame( 456, $srv->remotePort() );
    }


    public function testRequestMethod() : void {
        $srv = new Server( [
            'REQUEST_METHOD' => 'foo',
        ] );
        self::assertSame( 'foo', $srv->requestMethod() );

        $srv = $srv->withRequestMethod( 'bar' );
        self::assertSame( 'bar', $srv->requestMethod() );
    }


    public function testRequestScheme() : void {
        $srv = new Server( [
            'REQUEST_SCHEME' => 'foo',
        ] );
        self::assertSame( 'foo', $srv->requestScheme() );

        $srv = $srv->withRequestScheme( 'bar' );
        self::assertSame( 'bar', $srv->requestScheme() );
    }


    public function testRequestUri() : void {
        $srv = new Server( [
            'REQUEST_URI' => 'foo',
        ] );
        self::assertSame( 'foo', $srv->requestUri() );

        $srv = $srv->withRequestUri( 'bar' );
        self::assertSame( 'bar', $srv->requestUri() );
    }


    public function testScriptFilename() : void {
        $srv = new Server( [
            'SCRIPT_FILENAME' => 'foo',
        ] );
        self::assertSame( 'foo', $srv->scriptFilename() );

        $srv = $srv->withScriptFilename( 'bar' );
        self::assertSame( 'bar', $srv->scriptFilename() );
    }


    public function testScriptName() : void {
        $srv = new Server( [
            'SCRIPT_NAME' => 'foo',
        ] );
        self::assertSame( 'foo', $srv->scriptName() );

        $srv = $srv->withScriptName( 'bar' );
        self::assertSame( 'bar', $srv->scriptName() );
    }


    public function testServerAddr() : void {
        $srv = new Server( [
            'SERVER_ADDR' => 'foo',
        ] );
        self::assertSame( 'foo', $srv->serverAddr() );

        $srv = $srv->withServerAddr( 'bar' );
        self::assertSame( 'bar', $srv->serverAddr() );
    }


    public function testServerName() : void {
        $srv = new Server( [
            'SERVER_NAME' => 'foo',
        ] );
        self::assertSame( 'foo', $srv->serverName() );

        $srv = $srv->withServerName( 'bar' );
        self::assertSame( 'bar', $srv->serverName() );
    }


}
