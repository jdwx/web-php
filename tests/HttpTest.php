<?php


declare( strict_types = 1 );


use JDWX\Web\Backends\HttpBackendInterface;
use PHPUnit\Framework\TestCase;


final class HttpTest extends TestCase {


    public function testDefaultBackend() : void {
        $http = new class() extends JDWX\Web\Http {


            public static function peekBackend() : HttpBackendInterface {
                return self::backend();
            }


        };
        /** @noinspection PhpAccessStaticViaInstanceInspection */
        $http::clear();
        /** @noinspection PhpAccessStaticViaInstanceInspection */
        self::assertInstanceOf( JDWX\Web\Backends\PHPHttpBackend::class, $http::peekBackend() );
    }


    public function testSendHeader() : void {
        $backend = new JDWX\Web\Backends\MockHttpBackend();
        JDWX\Web\Http::init( $backend );
        JDWX\Web\Http::sendHeader( 'test' );
        self::assertSame( 'test', $backend->rHeaders[ 0 ] );
    }


    public function testSetResponseCode() : void {
        $backend = new JDWX\Web\Backends\MockHttpBackend();
        JDWX\Web\Http::init( $backend );
        self::assertSame( 200, $backend->iStatus );
        JDWX\Web\Http::setResponseCode( 12345 );
        self::assertSame( 12345, $backend->iStatus );
        JDWX\Web\Http::setResponseCode( 500 );
        self::assertSame( 500, $backend->iStatus );
    }


}
