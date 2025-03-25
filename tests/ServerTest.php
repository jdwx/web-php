<?php


declare( strict_types = 1 );


use JDWX\Web\Server;
use PHPUnit\Framework\TestCase;


class ServerTest extends TestCase {


    public function testRemoteAddr() : void {
        $_SERVER['REMOTE_ADDR'] = '192.0.2.1';
        $server = new Server();
        self::assertSame( '192.0.2.1', $server->remoteAddr() );
    }


}