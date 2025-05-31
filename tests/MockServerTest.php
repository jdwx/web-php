<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests;


use JDWX\Web\Backends\MockServer;
use PHPUnit\Framework\TestCase;


class MockServerTest extends TestCase {


    public function testPOST() : void {
        $srv = MockServer::POST();
        self::assertSame( 'POST', $srv->requestMethod() );
    }


}
