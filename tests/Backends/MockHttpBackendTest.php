<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Backends;


use JDWX\Web\Backends\AbstractHttpBackend;
use JDWX\Web\Backends\MockHttpBackend;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( AbstractHttpBackend::class )]
#[CoversClass( MockHttpBackend::class )]
final class MockHttpBackendTest extends TestCase {


    public function testGetHeaderForMultipleMatches() : void {
        $be = new MockHttpBackend();
        $be->setHeader( 'Set-Cookie: foo=bar' );
        $be->setHeader( 'Set-Cookie: baz=qux' );
        self::assertSame( [ 'foo=bar', 'baz=qux' ], $be->getHeader( 'Set-Cookie' ) );
    }


    public function testGetHeaderForNoMatch() : void {
        $be = new MockHttpBackend();
        $be->setHeader( 'Content-Type: text/html' );
        self::assertSame( [], $be->getHeader( 'X-Custom-Header' ) );
    }


    public function testGetHeaderForSingleMatch() : void {
        $be = new MockHttpBackend();
        $be->setHeader( 'Content-Type: text/html' );
        self::assertSame( 'text/html', $be->getHeader( 'Content-Type' ) );
    }


    public function testGetHeaderIsCaseInsensitive() : void {
        $be = new MockHttpBackend();
        $be->setHeader( 'Content-Type: text/html' );
        self::assertSame( 'text/html', $be->getHeader( 'content-type' ) );
        self::assertSame( 'text/html', $be->getHeader( 'CONTENT-TYPE' ) );
    }


    public function testGetHeaderWithNoHeaders() : void {
        $be = new MockHttpBackend();
        self::assertSame( [], $be->getHeader( 'Content-Type' ) );
    }


    public function testGetResponseCode() : void {
        $be = new MockHttpBackend();
        self::assertSame( 200, $be->getResponseCode() );
    }


    public function testHeadersSent() : void {
        $be = new MockHttpBackend();
        self::assertFalse( $be->headersSent() );
        MockHttpBackend::$bHeadersSent = true;
        self::assertTrue( $be->headersSent() );
    }


    public function testSetHeader() : void {
        $be = new MockHttpBackend();
        $be->setHeader( 'Content-Type: application/json' );
        $be->setHeader( 'X-Custom-Header: custom-value' );
        self::assertSame( [ 'Content-Type: application/json', 'X-Custom-Header: custom-value' ], $be->rHeaders );
    }


    public function testSetResponseCode() : void {
        $be = new MockHttpBackend();
        $be->setResponseCode( 404 );
        self::assertSame( 404, $be->getResponseCode() );

        $be->setResponseCode( 500 );
        self::assertSame( 500, $be->getResponseCode() );
    }


    protected function setUp() : void {
        MockHttpBackend::$bHeadersSent = false;
    }


}
