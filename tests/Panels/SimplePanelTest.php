<?php


declare( strict_types = 1 );


namespace Panels;


use JDWX\Web\Panels\SimplePanel;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( SimplePanel::class )]
final class SimplePanelTest extends TestCase {


    public function testAddBody() : void {
        $panel = new SimplePanel( 'Hello' );
        $panel->addBody( 'World' );
        self::assertSame( [ 'Hello', 'World' ], $panel->body() );
    }


    public function testBody() : void {
        $panel = new SimplePanel( 'Hello' );
        self::assertSame( [ 'Hello' ], $panel->body() );
    }


}