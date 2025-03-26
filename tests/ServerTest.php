<?php


declare( strict_types = 1 );


use JDWX\Web\Server;
use PHPUnit\Framework\TestCase;


class ServerTest extends TestCase {


    private Server $server;


    public function testDocumentRoot() : void {
        self::assertSame( '/var/www/html', $this->server->documentRoot() );
    }


    public function testHttpHost() : void {
        self::assertSame( 'example.com', $this->server->httpHost() );
    }


    public function testHttpReferer() : void {
        self::assertSame( 'https://referrer.com', $this->server->httpReferer() );
    }


    public function testHttpUserAgent() : void {
        self::assertSame( 'PHPUnit Test Browser', $this->server->httpUserAgent() );
    }


    public function testHttps() : void {
        self::assertTrue( $this->server->https() );

        $_SERVER[ 'HTTPS' ] = 'off';
        /** @noinspection PhpConditionAlreadyCheckedInspection */
        self::assertFalse( $this->server->https() );
    }


    public function testPathInfo() : void {
        self::assertSame( '/path/info', $this->server->pathInfo() );
    }


    public function testPhpSelf() : void {
        self::assertSame( '/index.php', $this->server->phpSelf() );
    }


    public function testRemoteAddr() : void {
        self::assertSame( '192.0.2.1', $this->server->remoteAddr() );
    }


    public function testRemotePort() : void {
        self::assertSame( 12345, $this->server->remotePort() );
        self::assertIsInt( $this->server->remotePort() );
    }


    public function testRequestMethod() : void {
        self::assertSame( 'GET', $this->server->requestMethod() );
    }


    public function testRequestScheme() : void {
        self::assertSame( 'https', $this->server->requestScheme() );
    }


    public function testRequestUri() : void {
        self::assertSame( '/test/path', $this->server->requestUri() );
    }


    public function testScriptFilename() : void {
        self::assertSame( '/var/www/html/index.php', $this->server->scriptFilename() );
    }


    public function testScriptName() : void {
        self::assertSame( '/index.php', $this->server->scriptName() );
    }


    public function testServerAddr() : void {
        self::assertSame( '203.0.113.1', $this->server->serverAddr() );
    }


    public function testServerName() : void {
        self::assertSame( 'server.example.com', $this->server->serverName() );
    }


    protected function setUp() : void {
        parent::setUp();

        // Set up default $_SERVER values for testing
        $_SERVER[ 'DOCUMENT_ROOT' ] = '/var/www/html';
        $_SERVER[ 'HTTP_HOST' ] = 'example.com';
        $_SERVER[ 'HTTP_REFERER' ] = 'https://referrer.com';
        $_SERVER[ 'HTTP_USER_AGENT' ] = 'PHPUnit Test Browser';
        $_SERVER[ 'HTTPS' ] = 'on';
        $_SERVER[ 'PATH_INFO' ] = '/path/info';
        $_SERVER[ 'PHP_SELF' ] = '/index.php';
        $_SERVER[ 'REMOTE_ADDR' ] = '192.0.2.1';
        $_SERVER[ 'REMOTE_PORT' ] = '12345';
        $_SERVER[ 'REQUEST_METHOD' ] = 'GET';
        $_SERVER[ 'REQUEST_SCHEME' ] = 'https';
        $_SERVER[ 'REQUEST_URI' ] = '/test/path';
        $_SERVER[ 'SCRIPT_FILENAME' ] = '/var/www/html/index.php';
        $_SERVER[ 'SCRIPT_NAME' ] = '/index.php';
        $_SERVER[ 'SERVER_ADDR' ] = '203.0.113.1';
        $_SERVER[ 'SERVER_NAME' ] = 'server.example.com';

        $this->server = new Server();
    }


}
