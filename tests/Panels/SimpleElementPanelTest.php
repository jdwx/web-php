<?php


declare( strict_types = 1 );


namespace Panels;


use JDWX\Web\Panels\SimpleElementPanel;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( SimpleElementPanel::class )]
final class SimpleElementPanelTest extends TestCase {


    public function testAddBody() : void {
        $panel = new SimpleElementPanel( 'foo', 'bar' );
        $panel->addBody( 'baz' );
        $panel->addBody( 'qux' );
        self::assertSame(
            [ '<foo>', 'bar', 'baz', 'qux', '</foo>' ],
            iterator_to_array( $panel->body(), false )
        );
    }


    public function testConstruct() : void {
        $panel = new SimpleElementPanel( 'foo', 'bar' );
        self::assertSame( [ '<foo>', 'bar', '</foo>' ], iterator_to_array( $panel->body(), false ) );
    }


}
