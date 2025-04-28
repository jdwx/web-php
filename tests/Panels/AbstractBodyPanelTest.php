<?php


declare( strict_types = 1 );


namespace Panels;


use JDWX\Web\Panels\AbstractBodyPanel;
use JDWX\Web\Panels\ScriptUri;
use JDWX\Web\Panels\SimplePanel;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( AbstractBodyPanel::class )]
final class AbstractBodyPanelTest extends TestCase {


    public function testAddCssUri() : void {
        $panel = new SimplePanel( 'foo' );
        $panel->addCssUri( 'URI' );
        self::assertSame( [ 'URI' ], iterator_to_array( $panel->cssUris() ) );
        $panel->addCssUri( 'URI2' );
        self::assertSame(
            [ 'URI', 'URI2' ],
            iterator_to_array( $panel->cssUris() )
        );
    }


    public function testAddHeader() : void {
        $panel = new SimplePanel( 'foo' );
        $panel->addHeader( 'HEADER' );
        self::assertSame( [ 'HEADER' ], iterator_to_array( $panel->headers() ) );
        $panel->addHeader( 'HEADER2', 'VALUE' );
        self::assertSame(
            [ 'HEADER', 'HEADER2: VALUE' ],
            iterator_to_array( $panel->headers() )
        );
    }


    public function testAddScript() : void {
        $panel = new SimplePanel( 'foo' );
        $script = new ScriptUri( 'URI' );
        $panel->addScript( $script );
        self::assertSame( [ $script ], iterator_to_array( $panel->scripts() ) );
        $script2 = new ScriptUri( 'URI2' );
        $panel->addScript( $script2 );
        self::assertSame(
            [ $script, $script2 ],
            iterator_to_array( $panel->scripts() )
        );
    }


    /** @noinspection HtmlUnknownTarget */
    public function testAddScriptUri() : void {
        $panel = new SimplePanel( 'foo' );
        $panel->addScriptUri( 'URI' );
        $out = array_map( function ( $x ) {
            return strval( $x );
        }, iterator_to_array( $panel->scripts() ) );
        self::assertSame( [ '<script src="URI"></script>' ], $out );
        $panel->addScriptUri( 'URI2' );
        $out = array_map( function ( $x ) {
            return strval( $x );
        }, iterator_to_array( $panel->scripts() ) );
        self::assertSame(
            [ '<script src="URI"></script>', '<script src="URI2"></script>' ],
            $out );
    }


}