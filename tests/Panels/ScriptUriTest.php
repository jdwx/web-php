<?php


declare( strict_types = 1 );


namespace Panels;


use JDWX\Web\Panels\ScriptUri;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( ScriptUri::class )]
final class ScriptUriTest extends TestCase {


    public function testSrc() : void {
        $script = new ScriptUri( 'URI' );
        /** @noinspection HtmlUnknownTarget */
        $st = '<script src="URI"></script>';
        self::assertSame( $st, strval( $script ) );
    }


}