<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests;


use InvalidArgumentException;
use JDWX\Web\Url;
use JDWX\Web\UrlParts;
use LogicException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;


#[CoversClass( Url::class )]
final class UrlTest extends TestCase {


    public function testHost() : void {

        $nstHost = Url::host( 'https://example.com/path/to/resource?query=string#fragment' );
        self::assertSame( 'example.com', $nstHost );

        $nstHost = Url::host( 'https://www.example.com:12345/a/b?foo=1&bar=baz' );
        self::assertSame( 'www.example.com', $nstHost );

        $nstHost = Url::host( '/foo/bar/baz' );
        self::assertNull( $nstHost );

    }


    public function testHostEx() : void {

        $nstHost = Url::hostEx( 'https://example.com/path/to/resource?query=string#fragment' );
        self::assertSame( 'example.com', $nstHost );

        self::expectException( RuntimeException::class );
        Url::hostEx( '/foo/bar/baz' );

    }


    public function testParent() : void {
        $st = 'https://user:pass@example.com:12345/path/to/resource?foo=bar&baz=qux#fragment';
        $st = Url::parent( $st );
        self::assertSame( 'https://user:pass@example.com:12345/path/to', $st );

        $st = Url::parent( $st );
        self::assertSame( 'https://user:pass@example.com:12345/path', $st );

        $st = Url::parent( $st );
        self::assertSame( 'https://user:pass@example.com:12345/', $st );

        $st = Url::parent( $st );
        self::assertSame( 'https://user:pass@example.com:12345/', $st );

        self::assertSame( '/foo', Url::parent( '/foo/bar' ) );
        self::assertSame( '/foo', Url::parent( '/foo/' ) );
        self::assertSame( '/', Url::parent( '/foo' ) );
        self::assertSame( '/', Url::parent( '/' ) );
    }


    public function testPath() : void {
        $stPath = Url::path( 'https://example.com/path/to/resource?query=string#fragment' );
        self::assertSame( '/path/to/resource', $stPath );

        $stPath = Url::path( 'https://www.example.com:12345/a/b?foo=1&bar=baz' );
        self::assertSame( '/a/b', $stPath );

        $stPath = Url::path( 'https://www.example.com:12345/a/b/?foo=1' );
        self::assertSame( '/a/b/', $stPath );

        $stPath = Url::path( 'https://www.example.com:12345/?foo=1' );
        self::assertSame( '/', $stPath );

        $stPath = Url::path( 'https://www.example.com:12345?foo=1' );
        self::assertSame( '/', $stPath );

        $stPath = Url::path( 'https://www.example.com:12345' );
        self::assertSame( '/', $stPath );

        $stPath = Url::path( '/foo/bar/baz' );
        self::assertSame( '/foo/bar/baz', $stPath );
    }


    public function testScheme() : void {
        $stScheme = Url::scheme( 'https://example.com/path/to/resource?query=string#fragment' );
        self::assertSame( 'https', $stScheme );

        /** @noinspection HttpUrlsUsage */
        $stScheme = Url::scheme( 'http://example.com/path/to/resource?query=string#fragment' );
        self::assertSame( 'http', $stScheme );

        $stScheme = Url::scheme( 'imap://imap.example.com/;TYPE=LIST' );
        self::assertSame( 'imap', $stScheme );

        self::assertNull( Url::scheme( '/foo/bar/baz' ) );
    }


    public function testSchemeEx() : void {
        $stScheme = Url::schemeEx( 'https://example.com/path/to/resource?query=string#fragment' );
        self::assertSame( 'https', $stScheme );

        self::expectException( RuntimeException::class );
        Url::schemeEx( '/foo/bar/baz' );
    }


    public function testSetQueryParam() : void {
        $parts = Url::split( '/a/b?foo=1&bar=baz' );
        self::expectException( LogicException::class );
        $parts[ 'foo' ] = '2';
    }


    public function testSplitExForInvalid() : void {
        self::expectException( InvalidArgumentException::class );
        Url::splitEx( '\\absolute-nonsense\\' );
    }


    public function testSplitExForValidUri() : void {
        $parts = Url::splitEx( '/a/b?foo=1&bar=baz' );
        self::assertNull( $parts->nstScheme );
        self::assertNull( $parts->nstUser );
        self::assertNull( $parts->nstPassword );
        self::assertNull( $parts->nstHost );
        self::assertNull( $parts->nuPort );
        self::assertSame( [ 'a' ], $parts->subFolders );
        self::assertSame( 'b', $parts->nstFile );
        self::assertSame( '1', $parts[ 'foo' ] );
        self::assertSame( 'baz', $parts[ 'bar' ] );
    }


    public function testSplitExForValidUrl() : void {
        $parts = Url::splitEx( 'https://user:pass@example.com:12345/a/b?foo=1&bar=baz' );
        self::assertSame( 'https', $parts->nstScheme );
        self::assertSame( 'user', $parts->nstUser );
        self::assertSame( 'pass', $parts->nstPassword );
        self::assertSame( 'example.com', $parts->nstHost );
        self::assertSame( 12345, $parts->nuPort );
        self::assertSame( [ 'a' ], $parts->subFolders );
        self::assertSame( 'b', $parts->nstFile );
        self::assertSame( '1', $parts[ 'foo' ] );
        self::assertSame( 'baz', $parts[ 'bar' ] );
    }


    public function testSplitForArray() : void {
        $parts = Url::split( '/a/b?foo[]=1&foo[]=2' );
        self::assertInstanceOf( UrlParts::class, $parts );
        self::assertSame( [ 'a' ], $parts->subFolders );
        self::assertSame( 'b', $parts->nstFile );
        self::assertSame( [ '1', '2' ], $parts[ 'foo' ] );
    }


    public function testSplitForEscaped() : void {
        $parts = Url::split( '/foo/bar?baz=qux%20quux' );
        self::assertInstanceOf( UrlParts::class, $parts );
        self::assertSame( 'qux quux', $parts[ 'baz' ] );
    }


    public function testSplitForFragment() : void {
        $parts = Url::split( '/a/b#fragment' );
        self::assertInstanceOf( UrlParts::class, $parts );
        self::assertSame( 'fragment', $parts->nstFragment );
    }


    public function testSplitForInvalid() : void {
        self::assertNull( Url::split( '\\absolute-nonsense\\' ) );
        self::assertNull( Url::split( 'https://example.com,http://http' ) );
        self::assertNull( Url::split( 'https://example.com/path/te st/resource?query=string#fragment' ) );
    }


    public function testSplitForRoot() : void {
        $parts = Url::splitEx( '/' );
        self::assertSame( [], $parts->subFolders );
        self::assertNull( $parts->nstFile );
        self::assertEmpty( $parts->rQuery );
    }


    public function testSplitForSubFolder() : void {
        $parts = Url::splitEx( '/a/' );
        self::assertSame( [ 'a' ], $parts->subFolders );
        self::assertNull( $parts->nstFile );
        self::assertEmpty( $parts->rQuery );
    }


    public function testSplitForValid() : void {
        $parts = Url::split( '/a/b?foo=1&bar=baz' );
        self::assertInstanceOf( UrlParts::class, $parts );
        self::assertSame( [ 'a' ], $parts->subFolders );
        self::assertSame( 'b', $parts->nstFile );
        self::assertSame( '1', $parts[ 'foo' ] );
        self::assertSame( 'baz', $parts[ 'bar' ] );
        self::assertFalse( isset( $parts[ 'qux' ] ) );
    }


    public function testUnsetQueryParam() : void {
        $parts = Url::split( '/a/b?foo=1&bar=baz' );
        self::expectException( LogicException::class );
        unset( $parts[ 'foo' ] );
    }


    public function testValidatePathSegment() : void {
        self::assertTrue( Url::validatePathSegment( 'test' ) );
        self::assertTrue( Url::validatePathSegment( 'test-123' ) );
        self::assertTrue( Url::validatePathSegment( 'test_123' ) );
        self::assertTrue( Url::validatePathSegment( 'test.123' ) );
        self::assertTrue( Url::validatePathSegment( 'test%20' ) );
        self::assertTrue( Url::validatePathSegment( null ) );
        self::assertFalse( Url::validatePathSegment( 'test/' ) );
        self::assertFalse( Url::validatePathSegment( 'test?' ) );
        self::assertFalse( Url::validatePathSegment( 'test#' ) );
        self::assertFalse( Url::validatePathSegment( 'tes%t' ) );
        self::assertFalse( Url::validatePathSegment( '..' ) );
        self::assertFalse( Url::validatePathSegment( '.' ) );
        self::assertFalse( Url::validatePathSegment( '' ) );
    }


}
