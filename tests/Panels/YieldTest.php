<?php


declare( strict_types = 1 );


namespace Panels;


use PHPUnit\Framework\TestCase;


class YieldTest extends TestCase {


    public function testYield() : void {
        $page = new class() {


            use \JDWX\Web\Panels\YieldTrait;


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


}
