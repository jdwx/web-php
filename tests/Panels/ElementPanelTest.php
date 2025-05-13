<?php


declare( strict_types = 1 );


namespace Panels;


use JDWX\Web\Panels\ElementPanel;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( ElementPanel::class )]
final class ElementPanelTest extends TestCase {


    public function testBodyForIterable() : void {
        $el = new class() extends ElementPanel {


            /** @return iterable<string> */
            protected function innerBody() : iterable {
                yield 'foo';
            }


        };
        self::assertSame( [ '<div>', 'foo', '</div>' ], iterator_to_array( $el->body(), false ) );
    }


    public function testBodyForString() : void {
        $el = new class() extends ElementPanel {


            protected function innerBody() : string {
                return 'bar';
            }


        };
        self::assertSame( [ '<div>', 'bar', '</div>' ], iterator_to_array( $el->body(), false ) );
    }


    public function testSetAttribute() : void {
        $el = new class() extends ElementPanel {


            protected function innerBody() : string {
                return 'baz';
            }


        };
        $el->setAttribute( 'id', 'bar' );
        $el->setAttribute( 'contenteditable' );
        self::assertSame(
            [ '<div contenteditable id="bar">', 'baz', '</div>' ],
            iterator_to_array( $el->body(), false )
        );
    }


    public function testSetElement() : void {
        $el = new class() extends ElementPanel {


            protected function innerBody() : string {
                return 'baz';
            }


        };
        $el->setElement( 'span' );
        self::assertSame( [ '<span>', 'baz', '</span>' ], iterator_to_array( $el->body(), false ) );
    }


}
