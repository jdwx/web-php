<?php


declare( strict_types = 1 );


namespace Panels;


use JDWX\Web\Panels\HeaderListTrait;
use PHPUnit\Framework\TestCase;


class HeaderListTest extends TestCase {


    public function testAddHeader() : void {
        $obj = $this->newObject();
        $obj->addHeader( 'foo' );
        self::assertSame( [ 'foo' ], iterator_to_array( $obj->headerList() ) );
        $obj->addHeader( 'bar', 'baz' );
        self::assertSame(
            [ 'foo', 'bar: baz' ],
            iterator_to_array( $obj->headerList() )
        );
    }


    private function newObject() : object {
        return new class() {


            use HeaderListTrait;


            public function text() : string {
                $st = '';
                /** @noinspection PhpLoopCanBeReplacedWithImplodeInspection */
                foreach ( $this->headerList() as $header ) {
                    $st .= $header;
                }
                return $st;
            }


        };
    }


}
