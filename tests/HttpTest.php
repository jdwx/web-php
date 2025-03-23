<?php


declare( strict_types = 1 );


use JDWX\Web\Backends\HttpBackendInterface;
use JDWX\Web\Backends\MockHttpBackend;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( JDWX\Web\Http::class )]
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


    public function testGetResponseCode() : void {
        $backend = new JDWX\Web\Backends\MockHttpBackend();
        JDWX\Web\Http::init( $backend );

        $backend->setResponseCode( 200 );
        self::assertSame( 200, JDWX\Web\Http::getResponseCode() );

        $backend->setResponseCode( 12345 );
        self::assertSame( 12345, JDWX\Web\Http::getResponseCode() );

        $backend->setResponseCode( 500 );
        self::assertSame( 500, JDWX\Web\Http::getResponseCode() );
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


    public function testSetHeader() : void {
        $backend = new JDWX\Web\Backends\MockHttpBackend();
        JDWX\Web\Http::init( $backend );
        JDWX\Web\Http::setHeader( 'test' );
        JDWX\Web\Http::setHeader( 'foo', 'bar' );
        self::assertSame( 'test', $backend->rHeaders[ 0 ] );
        self::assertSame( 'foo: bar', $backend->rHeaders[ 1 ] );
    }


    public function testSetHeaders() : void {
        $backend = new JDWX\Web\Backends\MockHttpBackend();
        JDWX\Web\Http::init( $backend );
        JDWX\Web\Http::setHeaders( [ 'test', 'foo: bar' ] );
        self::assertSame( 'test', $backend->rHeaders[ 0 ] );
        self::assertSame( 'foo: bar', $backend->rHeaders[ 1 ] );
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
