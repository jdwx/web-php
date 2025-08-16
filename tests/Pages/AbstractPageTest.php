<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Pages;


use JDWX\Web\Flush;
use JDWX\Web\Pages\AbstractPage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Stringable;


#[CoversClass( AbstractPage::class )]
final class AbstractPageTest extends TestCase {


    public function testEcho() : void {
        $page = $this->newAbstractPage( 'text/plain', 'TEST_CONTENT' );
        ob_start();
        $page->echo();
        $result = ob_get_clean();
        self::assertSame( 'TEST_CONTENT', $result );
    }


    /** @suppress PhanTypeMismatchArgument, PhanTypeInvalidDimOffset */
    public function testEchoForFlush() : void {
        $page = $this->newAbstractPage( 'text/plain', [ 'TEST', new Flush(), 'CONTENT' ] );
        $r = [];
        $fn = static function ( $stChunk ) use ( &$r ) {
            $r[] = $stChunk;
        };
        ob_start( $fn );
        $page->echo();
        ob_end_clean();
        self::assertSame( 'TEST', $r[ 0 ] );
        self::assertSame( 'CONTENT', $r[ 1 ] );
    }


    public function testGetCharset() : void {
        $page = $this->newAbstractPage( 'text/html' );
        self::assertNull( $page->getCharset() );
        $page->setCharset( 'foo' );
        self::assertSame( 'foo', $page->getCharset() );
        $page->setCharset( null );
        self::assertNull( $page->getCharset() );
    }


    public function testGetContentType() : void {
        $page = $this->newAbstractPage( 'text/html' );
        self::assertEquals( 'text/html', $page->getContentType() );
    }


    public function testGetFullContentType() : void {
        $page = $this->newAbstractPage( 'foo' );
        self::assertEquals( 'foo', $page->getFullContentType() );
        $page->setCharset( 'bar' );
        self::assertEquals( 'foo; charset=bar', $page->getFullContentType() );
    }


    public function testGetHeaders() : void {
        $page = $this->newAbstractPage( 'foo' );
        $r = iterator_to_array( $page->getHeaders(), false );
        self::assertCount( 1, $r );
        self::assertEquals( 'Content-Type: foo', $r[ 0 ] );
    }


    public function testHasCharset() : void {
        $page = $this->newAbstractPage( 'text/html' );
        self::assertFalse( $page->hasCharset() );
        $page->setCharset( 'foo' );
        self::assertTrue( $page->hasCharset() );
        $page->setCharset( null );
        self::assertFalse( $page->hasCharset() );
    }


    public function testToString() : void {
        $page = $this->newAbstractPage( 'text/plain', 'TEST_CONTENT' );
        self::assertSame( $page->render(), strval( $page ) );
    }


    /** @param string|iterable<string|Stringable> $i_content */
    private function newAbstractPage( string $i_stContentType, string|iterable $i_content = '' ) : AbstractPage {
        return new class ( $i_stContentType, $i_content ) extends AbstractPage {


            /** @param string|iterable<string|Stringable> $content */
            public function __construct( string $i_stContentType, private readonly string|iterable $content ) {
                parent::__construct( $i_stContentType );
            }


            public function stream() : \Generator {
                if ( is_string( $this->content ) ) {
                    yield $this->content;
                    return;
                }
                foreach ( $this->content as $stChunk ) {
                    yield $stChunk;
                }
            }


        };

    }


}