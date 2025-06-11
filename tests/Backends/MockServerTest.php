<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Backends;


use JDWX\Web\Backends\MockServer;
use PHPUnit\Framework\TestCase;


class MockServerTest extends TestCase {


    public function testNew() : void {
        $srv = MockServer::new();
        self::assertSame( 'GET', $srv->requestMethod() );
        self::assertSame( 12345, $srv->remotePort() );
    }


    public function testPOST() : void {
        $srv = MockServer::POST();
        self::assertSame( 'POST', $srv->requestMethod() );
        self::assertSame( 12345, $srv->remotePort() );
    }


}
