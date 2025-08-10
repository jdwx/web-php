<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Framework;


use JDWX\Web\Framework\AbstractResponse;
use JDWX\Web\Pages\PageInterface;
use JDWX\Web\Pages\SimpleTextPage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( AbstractResponse::class )]
final class AbstractResponseTest extends TestCase {


    public function testGetHeader() : void {
        $rsp = $this->newAbstractResponse();
        self::assertNull( $rsp->getHeader( 'X-Foo' ) );

        $rsp = $this->newAbstractResponse( i_rHeaders: [ 'X-Foo: Bar' ] );
        self::assertSame( 'Bar', $rsp->getHeader( 'X-Foo' ) );
        self::assertSame( 'Bar', $rsp->getHeader( 'x-foo' ) );
        self::assertNull( $rsp->getHeader( 'X-Bar' ) );

        $rsp = $this->newAbstractResponse( i_rHeaders: [ 'x-foo: Bar' ] );
        self::assertSame( 'Bar', $rsp->getHeader( 'X-Foo' ) );
        self::assertSame( 'Bar', $rsp->getHeader( 'x-foo' ) );

    }


    public function testGetHeaders() : void {
        $response = $this->newAbstractResponse();
        self::assertSame( [], $response->getHeaders()->toArray() );

        $response = $this->newAbstractResponse( new SimpleTextPage( 'foo' ), 200, [ 'X-Foo: Bar' ] );
        self::assertSame( [ 'X-Foo: Bar' ], $response->getHeaders()->toArray() );
    }


    public function testGetPage() : void {
        $page = new SimpleTextPage( 'foo' );
        $response = $this->newAbstractResponse( $page );
        self::assertSame( $page, $response->getPage() );
    }


    public function testGetStatusCode() : void {
        $response = $this->newAbstractResponse();
        self::assertSame( 200, $response->getStatusCode() );

        $response = $this->newAbstractResponse( new SimpleTextPage( 'foo' ), 404 );
        self::assertSame( 404, $response->getStatusCode() );
    }


    public function testToString() : void {
        $response = $this->newAbstractResponse();
        self::assertSame( 'foo', strval( $response ) );
    }


    /** @param ?iterable<string> $i_rHeaders */
    private function newAbstractResponse( PageInterface|string $i_page = 'foo', int $i_uStatusCode = 200,
                                          ?iterable            $i_rHeaders = null ) : AbstractResponse {
        if ( is_string( $i_page ) ) {
            $i_page = new SimpleTextPage( $i_page );
        }
        return new readonly class( $i_page, $i_uStatusCode, $i_rHeaders ) extends AbstractResponse {


        };
    }


}
