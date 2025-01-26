<?php


declare( strict_types = 1 );


use JDWX\Web\Backends\HttpBackendInterface;
use JDWX\Web\Backends\MockHttpBackend;
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


    /**
     * This is kind of sad, but we can't actually test this under PHPUnit.
     *
     * @return void
     */
    public function testHeadersSent() : void {
        $backend = new MockHttpBackend();
        JDWX\Web\Http::init( $backend );
        self::assertFalse( JDWX\Web\Http::headersSent() );
        MockHttpBackend::$bHeadersSent = true;
        self::assertTrue( JDWX\Web\Http::headersSent() );
    }


    public function testSendHeader() : void {
        $backend = new JDWX\Web\Backends\MockHttpBackend();
        JDWX\Web\Http::init( $backend );
        JDWX\Web\Http::setHeader( 'test' );
        self::assertSame( 'test', $backend->rHeaders[ 0 ] );
    }


    public function testSetResponseCode() : void {
        $backend = new JDWX\Web\Backends\MockHttpBackend();
        JDWX\Web\Http::init( $backend );
        self::assertSame( 200, $backend->getResponseCode() );
        JDWX\Web\Http::setResponseCode( 12345 );
        self::assertSame( 12345, $backend->getResponseCode() );
        JDWX\Web\Http::setResponseCode( 500 );
        self::assertSame( 500, $backend->getResponseCode() );
    }


}
