<?php


declare( strict_types = 1 );


namespace Panels;


use JDWX\Web\Panels\AbstractPanel;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( AbstractPanel::class )]
final class AbstractPanelTest extends TestCase {


    /**
     * The class we are testing has almost no functionality.
     */
    public function testSimple() : void {
        $panel = $this->newPanel();
        $panel->first();
        self::assertSame( [], $panel->headers() );
        self::assertSame( [], $panel->cssUris() );
        self::assertSame( '', $panel->head() );
        self::assertSame( '', $panel->bodyEarly() );
        self::assertSame( 'BODY', $panel->body() );
        self::assertSame( '', $panel->bodyLate() );
        self::assertSame( [], $panel->scripts() );
        $panel->last();
    }


    private function newPanel() : AbstractPanel {
        return new class( 'BODY' ) extends AbstractPanel {


            public function __construct( private readonly string $stBody ) { }


            public function body() : string {
                return $this->stBody;
            }


        };
    }


}