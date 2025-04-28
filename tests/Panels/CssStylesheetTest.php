<?php


declare( strict_types = 1 );


namespace Panels;


use JDWX\Web\Panels\CssStylesheet;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( CssStylesheet::class )]
final class CssStylesheetTest extends TestCase {


    /** @noinspection HtmlUnknownTarget */
    public function testBasic() : void {
        $css = new CssStylesheet( 'foo' );
        self::assertEquals( '<link href="foo" rel="stylesheet">', strval( $css ) );
    }


}
