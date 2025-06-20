<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Framework;


use JDWX\Web\Framework\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( Response::class )]
final class ResponseTest extends TestCase {


    public function testBinary() : void {
        $response = Response::binary( 'FOO_BAR' );
        self::assertSame( 'FOO_BAR', strval( $response->getPage() ) );
        self::assertSame( 200, $response->getStatusCode() );
        self::assertSame( [], $response->getHeaders()->toArray() );
    }


    public function testHtml() : void {
        $response = Response::html( '<h1>Hello, world!</h1>', 200, [ 'X-Foo: Bar' ] );
        self::assertStringContainsString( '<h1>Hello, world!</h1>', strval( $response->getPage() ) );
        self::assertSame( 200, $response->getStatusCode() );
        self::assertSame( [ 'X-Foo: Bar' ], $response->getHeaders()->toArray() );
    }


    public function testJson() : void {
        $response = Response::json( [ 'foo' => 'bar' ], 200, [ 'X-Foo: Bar' ] );
        self::assertStringContainsString( '{"foo":"bar"}', strval( $response->getPage() ) );
        self::assertSame( 200, $response->getStatusCode() );
        self::assertSame( [ 'X-Foo: Bar' ], $response->getHeaders()->toArray() );
    }


    public function testRedirectPermanentWithGet() : void {
        $response = Response::redirectPermanentWithGet( '/foo/bar' );
        self::assertSame( 301, $response->getStatusCode() );
        self::assertSame( [ 'Location: /foo/bar' ], $response->getHeaders()->toArray() );
    }


    public function testRedirectPermanentWithSameMethod() : void {
        $response = Response::redirectPermanentWithSameMethod( '/foo/bar' );
        self::assertSame( 308, $response->getStatusCode() );
        self::assertSame( [ 'Location: /foo/bar' ], $response->getHeaders()->toArray() );
    }


    public function testRedirectTemporaryWithGet() : void {
        $response = Response::redirectTemporaryWithGet( '/foo/bar' );
        self::assertSame( 303, $response->getStatusCode() );
        self::assertSame( [ 'Location: /foo/bar' ], $response->getHeaders()->toArray() );
    }


    public function testRedirectTemporaryWithSameMethod() : void {
        $response = Response::redirectTemporaryWithSameMethod( '/foo/bar' );
        self::assertSame( 307, $response->getStatusCode() );
        self::assertSame( [ 'Location: /foo/bar' ], $response->getHeaders()->toArray() );
    }


    public function testText() : void {
        $response = Response::text( 'Hello, world!', 200, [ 'X-Foo: Bar' ] );
        self::assertSame( 'Hello, world!', strval( $response->getPage() ) );
        self::assertSame( 200, $response->getStatusCode() );
        self::assertSame( [ 'X-Foo: Bar' ], $response->getHeaders()->toArray() );
    }


}
