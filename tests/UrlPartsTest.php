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


    public function testIsSafeWeb() : void {
        self::assertTrue( Url::splitEx( 'https://example.com/foo' )->isSafeWeb() );
        self::assertTrue( Url::splitEx( 'http://example.com/foo' )->isSafeWeb() );

        # Wrong scheme but valid host.
        self::assertFalse( Url::splitEx( 'ftp://example.com/foo' )->isSafeWeb() );

        # Allowed scheme but no host (relative URL).
        self::assertFalse( Url::splitEx( '/foo/bar' )->isSafeWeb() );

        # javascript: parses without a host — classic open-redirect / XSS bypass.
        self::assertFalse( Url::splitEx( 'javascript:alert(1)' )->isSafeWeb() );

        # Custom allow list.
        self::assertTrue(
            Url::splitEx( 'ftp://files.example.com/foo' )->isSafeWeb( [ 'ftp', 'sftp' ] )
        );
        self::assertFalse(
            Url::splitEx( 'https://example.com/foo' )->isSafeWeb( [ 'ftp' ] )
        );

        # String form of allow list.
        self::assertTrue(
            Url::splitEx( 'https://example.com/foo' )->isSafeWeb( 'https' )
        );
        self::assertFalse(
            Url::splitEx( 'http://example.com/foo' )->isSafeWeb( 'https' )
        );
    }


    public function testIsSchemeAllowed() : void {
        self::assertTrue( Url::splitEx( 'https://example.com/foo' )->isSchemeAllowed() );
        self::assertTrue( Url::splitEx( 'http://example.com/foo' )->isSchemeAllowed() );
        self::assertFalse( Url::splitEx( 'ftp://example.com/foo' )->isSchemeAllowed() );
        self::assertFalse( Url::splitEx( 'javascript:alert(1)' )->isSchemeAllowed() );

        # Unlike isSafeWeb(), this returns true even without a host.
        self::assertFalse( Url::splitEx( '/foo/bar' )->isSchemeAllowed() );

        # Custom list (array).
        self::assertTrue(
            Url::splitEx( 'data:text/plain;base64,xxx' )->isSchemeAllowed( [ 'data' ] )
        );

        # Custom list (string).
        self::assertTrue(
            Url::splitEx( 'https://example.com/foo' )->isSchemeAllowed( 'https' )
        );
        self::assertFalse(
            Url::splitEx( 'http://example.com/foo' )->isSchemeAllowed( 'https' )
        );

        # Strict comparison: null scheme is never "allowed".
        $parts = Url::splitEx( '/no-scheme' );
        self::assertNull( $parts->nstScheme );
        self::assertFalse( $parts->isSchemeAllowed( [ 'http', 'https' ] ) );
    }


    public function testOffsetSet() : void {
        $url = Url::splitEx( 'https://example.com/path/to/resource?query=string#fragment' );
        $this->expectException( LogicException::class );
        $url[ 'query' ] = 'new_value';
        unset( $url );
    }


    public function testOffsetUnset() : void {
        $url = Url::splitEx( 'https://example.com/path/to/resource?query=string#fragment' );
        $this->expectException( LogicException::class );
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

        $parts = Url::splitEx( 'https://www.example.com:12345/a/b?foo=1&bar=baz' );
        self::assertSame( '/a/b', $parts->path() );

        $parts = Url::splitEx( 'https://www.example.com:12345/a/b/?foo=1' );
        self::assertSame( '/a/b/', $parts->path() );

        $parts = Url::splitEx( 'https://www.example.com:12345/?foo=1' );
        self::assertSame( '/', $parts->path() );

        $parts = Url::splitEx( 'https://www.example.com:12345?foo=1' );
        self::assertSame( '/', $parts->path() );

        $parts = Url::splitEx( 'https://www.example.com:12345' );
        self::assertSame( '/', $parts->path() );

        $paths = Url::splitEx( '/foo/bar/baz' );
        self::assertSame( '/foo/bar/baz', $paths->path() );

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
