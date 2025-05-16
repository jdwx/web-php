<?php


declare( strict_types = 1 );


namespace Panels;


use JDWX\Web\Panels\SimplePanel;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( SimplePanel::class )]
final class SimplePanelTest extends TestCase {


    public function testAddBodyForString() : void {
        $panel = new SimplePanel( 'Hello' );
        $panel->addBody( 'World' );
        self::assertSame( [ 'Hello', 'World' ], $panel->body() );
    }


    public function testAddBodyForStringable() : void {
        $panel = new SimplePanel( 'Hello' );
        $panel->addBody( new class() {


            public function __toString() : string {
                return 'World';
            }


        } );
        $body = $panel->body();
        assert( is_array( $body ) );
        self::assertSame( 'HelloWorld', join( '', $body ) );
    }


    public function testBody() : void {
        $panel = new SimplePanel( 'Hello' );
        self::assertSame( [ 'Hello' ], $panel->body() );
    }


}