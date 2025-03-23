<?php


declare( strict_types = 1 );


use JDWX\Web\AbstractPage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass(AbstractPage::class)]
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


    private function newAbstractPage( string $i_stContentType, string|iterable $i_content = '' ) : AbstractPage {
        return new class ( $i_stContentType, $i_content ) extends AbstractPage {

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