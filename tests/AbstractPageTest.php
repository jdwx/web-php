<?php


declare( strict_types = 1 );


use JDWX\Web\AbstractPage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( AbstractPage::class )]
final class AbstractPageTest extends TestCase {


    public function testEcho() : void {
        $page = $this->newAbstractPage( 'text/plain', 'TEST_CONTENT' );
        ob_start();
        $page->echo();
        $result = ob_get_clean();
        self::assertSame( 'TEST_CONTENT', $result );
    }


    public function testGetContentType() : void {
        $page = $this->newAbstractPage( 'text/html' );
        self::assertEquals( 'text/html', $page->getContentType() );
    }


    public function testToString() : void {
        $page = $this->newAbstractPage( 'text/plain', 'TEST_CONTENT' );
        self::assertSame( $page->render(), strval( $page ) );
    }


    public function testYield() : void {
        $page = new class() extends AbstractPage {


            public function __construct() {
                parent::__construct( 'text/plain' );
            }


            public function stream() : iterable {
                return [];
            }


            /**
             * @param string|iterable<string> $i_chunk
             * @return iterable<string>
             */
            public function yieldTest( string|iterable $i_chunk ) : iterable {
                return $this->yield( $i_chunk );
            }


        };
        self::assertSame(
            [ 'TEST_CONTENT' ],
            iterator_to_array( $page->yieldTest( 'TEST_CONTENT' ), false )
        );
        self::assertSame(
            [ 'TEST_CONTENT_1', 'TEST_CONTENT_2' ],
            iterator_to_array( $page->yieldTest( [ 'TEST_CONTENT_1', 'TEST_CONTENT_2' ] ), false )
        );
    }


    /** @param string|iterable<string> $i_content */
    private function newAbstractPage( string $i_stContentType, string|iterable $i_content = '' ) : AbstractPage {
        return new class ( $i_stContentType, $i_content ) extends AbstractPage {


            /** @param string|iterable<string> $content */
            public function __construct( string $i_stContentType, private readonly string|iterable $content ) {
                parent::__construct( $i_stContentType );
            }


            public function stream() : Generator {
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