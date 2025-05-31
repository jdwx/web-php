<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests;


use JDWX\Web\Backends\HttpBackendInterface;
use JDWX\Web\Backends\MockHttpBackend;
use JDWX\Web\Backends\PHPHttpBackend;
use JDWX\Web\Http;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( Http::class )]
final class HttpTest extends TestCase {


    public function testDefaultBackend() : void {
        $http = new class() extends Http {


            public static function peekBackend() : HttpBackendInterface {
                return self::backend();
            }


        };
        /** @noinspection PhpAccessStaticViaInstanceInspection */
        $http::clear();
        /** @noinspection PhpAccessStaticViaInstanceInspection */
        self::assertInstanceOf( PHPHttpBackend::class, $http::peekBackend() );
    }


    public function testGetResponseCode() : void {
        $backend = new MockHttpBackend();
        Http::init( $backend );

        $backend->setResponseCode( 200 );
        self::assertSame( 200, Http::getResponseCode() );

        $backend->setResponseCode( 12345 );
        self::assertSame( 12345, Http::getResponseCode() );

        $backend->setResponseCode( 500 );
        self::assertSame( 500, Http::getResponseCode() );
    }


    /**
     * This is kind of sad, but we can't test this under PHPUnit.
     *
     * @return void
     */
    public function testHeadersSent() : void {
        $backend = new MockHttpBackend();
        Http::init( $backend );
        self::assertFalse( Http::headersSent() );
        MockHttpBackend::$bHeadersSent = true;
        self::assertTrue( Http::headersSent() );
    }


    public function testSetHeader() : void {
        $backend = new MockHttpBackend();
        Http::init( $backend );
        Http::setHeader( 'test' );
        Http::setHeader( 'foo', 'bar' );
        self::assertSame( 'test', $backend->rHeaders[ 0 ] );
        self::assertSame( 'foo: bar', $backend->rHeaders[ 1 ] );
    }


    public function testSetHeaders() : void {
        $backend = new MockHttpBackend();
        Http::init( $backend );
        Http::setHeaders( [ 'test', 'foo: bar' ] );
        self::assertSame( 'test', $backend->rHeaders[ 0 ] );
        self::assertSame( 'foo: bar', $backend->rHeaders[ 1 ] );
    }


    public function testSetResponseCode() : void {
        $backend = new MockHttpBackend();
        Http::init( $backend );
        self::assertSame( 200, $backend->getResponseCode() );
        Http::setResponseCode( 12345 );
        self::assertSame( 12345, $backend->getResponseCode() );
        Http::setResponseCode( 500 );
        self::assertSame( 500, $backend->getResponseCode() );
    }


}
