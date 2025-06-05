<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests;


use JDWX\Web\Url;
use JDWX\Web\UrlParts;
use LogicException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( UrlParts::class )]
final class UrlPartsTest extends TestCase {


    public function testArrayAccess() : void {
        $url = Url::splitEx( 'https://example.com/path/to/resource?query=string#fragment' );
        self::assertTrue( isset( $url[ 'query' ] ) );
        self::assertSame( 'string', $url[ 'query' ] );
        self::assertFalse( isset( $url[ 'nonexistent' ] ) );
    }


    public function testOffsetSet() : void {
        $url = Url::splitEx( 'https://example.com/path/to/resource?query=string#fragment' );
        self::expectException( LogicException::class );
        $url[ 'query' ] = 'new_value';
    }


    public function testOffsetUnset() : void {
        $url = Url::splitEx( 'https://example.com/path/to/resource?query=string#fragment' );
        self::expectException( LogicException::class );
        unset( $url[ 'query' ] );
    }


    public function testParent() : void {
        $url = Url::splitEx( 'https://user:pass@example.com:12345/path/to/resource?foo=bar&baz=qux#fragment' );
        $url = $url->parent();
        self::assertSame( 'https://user:pass@example.com:12345/path/to', strval( $url ) );

        $url = $url->parent();
        self::assertSame( 'https://user:pass@example.com:12345/path', strval( $url ) );

        $url = $url->parent();
        self::assertSame( 'https://user:pass@example.com:12345/', strval( $url ) );

        $url = $url->parent();
        self::assertSame( 'https://user:pass@example.com:12345/', strval( $url ) );

        self::assertSame( '/foo', Url::splitEx( '/foo/bar' )->parent()->__toString() );
        self::assertSame( '/foo', Url::splitEx( '/foo/' )->parent()->__toString() );
        self::assertSame( '/', Url::splitEx( '/foo' )->parent()->__toString() );
        self::assertSame( '/', Url::splitEx( '/' )->parent()->__toString() );
    }


    public function testPath() : void {
        $url = Url::splitEx( 'https://example.com/path/to/resource?query=string#fragment' );
        self::assertSame( '/path/to/resource', $url->path() );
    }


    public function testPathOnly() : void {
        $url = Url::splitEx( 'https://user:pass@example.com:12345/path/to/resource?foo=bar&baz=qux#fragment' );
        self::assertSame( '/path/to/resource', $url->pathOnly()->__toString() );
    }


    public function testToString() : void {
        $st = 'https://user:pass@example.com:12345/path/to/resource?foo=bar&baz=qux#fragment';
        self::assertSame( $st, (string) Url::splitEx( $st ) );

        $st = 'https://user:pass@example.com:12345/path/to/?foo=bar&baz=qux';
        self::assertSame( $st, (string) Url::splitEx( $st ) );

        $st = 'https://user:pass@example.com:12345/path/to/resource#fragment';
        self::assertSame( $st, (string) Url::splitEx( $st ) );

        $st = 'https://user:pass@example.com:12345/path/to/?foo=bar&baz=qux#fragment';
        self::assertSame( $st, (string) Url::splitEx( $st ) );

        $st = 'https://user:pass@example.com:12345/path?foo=bar&baz=qux#fragment';
        self::assertSame( $st, (string) Url::splitEx( $st ) );

        $st = 'https://user:pass@example.com:12345/?foo=bar&baz=qux#fragment';
        self::assertSame( $st, (string) Url::splitEx( $st ) );

        $st = 'https://user:pass@example.com/path/to/resource?foo=bar&baz=qux#fragment';
        self::assertSame( $st, (string) Url::splitEx( $st ) );

        $st = 'https://user@example.com:12345/path/to/resource?foo=bar&baz=qux#fragment';
        self::assertSame( $st, (string) Url::splitEx( $st ) );

        $st = '//user@example.com:12345/path/to/resource?foo=bar&baz=qux#fragment';
        self::assertSame( $st, (string) Url::splitEx( $st ) );

        $st = '//example.com:12345/path/to/resource?foo=bar&baz=qux#fragment';
        self::assertSame( $st, (string) Url::splitEx( $st ) );

        $st = '//example.com/path/to/resource?foo=bar&baz=qux#fragment';
        self::assertSame( $st, (string) Url::splitEx( $st ) );

        $st = '/path/to/resource?foo=bar&baz=qux#fragment';
        self::assertSame( $st, (string) Url::splitEx( $st ) );

        $st = '/path/to/resource?foo=bar&baz=qux';
        self::assertSame( $st, (string) Url::splitEx( $st ) );

        $st = '/path/to/resource#fragment';
        self::assertSame( $st, (string) Url::splitEx( $st ) );

        $st = '/path/to/?foo=bar&baz=qux#fragment';
        self::assertSame( $st, (string) Url::splitEx( $st ) );

        $st = '/path/to?foo=bar&baz=qux#fragment';
        self::assertSame( $st, (string) Url::splitEx( $st ) );

        $st = '/path/?foo=bar&baz=qux#fragment';
        self::assertSame( $st, (string) Url::splitEx( $st ) );

        $st = '/path?foo=bar&baz=qux#fragment';
        self::assertSame( $st, (string) Url::splitEx( $st ) );

        $st = '/?foo=bar&baz=qux#fragment';
        self::assertSame( $st, (string) Url::splitEx( $st ) );

        $st = '/';
        self::assertSame( $st, (string) Url::splitEx( $st ) );

    }


    public function testValidate() : void {
        $url = Url::splitEx( 'https://example.com/path/to/resource?query=string#fragment' );
        self::assertTrue( $url->validate() );

        $url = Url::splitEx( 'https://example.com/path/te,st/resource?query=string#fragment' );
        self::assertTrue( $url->validate() );

        $url = Url::splitEx( 'https://example.com/path/%41/resource?query=string#fragment' );
        self::assertTrue( $url->validate() );

        $url = Url::splitEx( 'https://example.com/path//resource?query=string#fragment' );
        self::assertFalse( $url->validate() );

        $url = Url::splitEx( 'https://example.com/path/te%st/resource?query=string#fragment' );
        self::assertFalse( $url->validate() );

        $url = Url::splitEx( 'https://example.com/path/reso%urce?query=string#fragment' );
        self::assertFalse( $url->validate() );

        $url = Url::splitEx( 'https://example.com/path/../resource?query=string#fragment' );
        self::assertFalse( $url->validate() );

    }


}
