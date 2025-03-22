<?php


declare( strict_types = 1 );


use JDWX\Web\Url;
use JDWX\Web\UrlParts;
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


}
