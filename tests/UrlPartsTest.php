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


    public function testPath() : void {
        $url = Url::splitEx( 'https://example.com/path/to/resource?query=string#fragment' );
        self::assertSame( '/path/to/resource', $url->path() );
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
