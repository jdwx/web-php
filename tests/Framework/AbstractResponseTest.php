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


    public function testGetHeaders() : void {
        $response = $this->newAbstractResponse( new SimpleTextPage( 'foo' ) );
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
        $response = $this->newAbstractResponse( new SimpleTextPage( 'foo' ) );
        self::assertSame( 200, $response->getStatusCode() );

        $response = $this->newAbstractResponse( new SimpleTextPage( 'foo' ), 404 );
        self::assertSame( 404, $response->getStatusCode() );
    }


    public function testToString() : void {
        $response = $this->newAbstractResponse( new SimpleTextPage( 'foo' ) );
        self::assertSame( 'foo', strval( $response ) );
    }


    /** @param ?iterable<string> $i_rHeaders */
    private function newAbstractResponse( PageInterface $i_page, int $i_uStatusCode = 200,
                                          ?iterable     $i_rHeaders = null ) : AbstractResponse {
        return new readonly class( $i_page, $i_uStatusCode, $i_rHeaders ) extends AbstractResponse {


        };
    }


}
