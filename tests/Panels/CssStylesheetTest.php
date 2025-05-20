<?php


declare( strict_types = 1 );


namespace Panels;


use JDWX\Web\Panels\CssLink;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( CssLink::class )]
final class CssStylesheetTest extends TestCase {


    /** @noinspection HtmlUnknownTarget */
    public function testBasic() : void {
        $css = new CssLink( 'foo' );
        self::assertEquals( '<link href="foo" rel="stylesheet">', strval( $css ) );
    }


}
