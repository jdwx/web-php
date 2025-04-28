<?php


declare( strict_types = 1 );


namespace Panels;


use JDWX\Web\Panels\CssInline;
use PHPUnit\Framework\TestCase;


class CssInlineTest extends TestCase {


    public function testBasic() : void {
        $css = new CssInline( '.foo' );
        self::assertEquals( '<style>.foo</style>', strval( $css ) );
    }


}
