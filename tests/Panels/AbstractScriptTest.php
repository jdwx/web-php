<?php


declare( strict_types = 1 );


namespace Panels;


use JDWX\Web\Panels\AbstractScript;
use JDWX\Web\Panels\ScriptUri;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( AbstractScript::class )]
final class AbstractScriptTest extends TestCase {


    public function testAsync() : void {
        $script = new ScriptUri( 'URI' );
        $script->setAsync();
        /** @noinspection HtmlUnknownTarget */
        $st = '<script async src="URI"></script>';
        self::assertSame( $st, strval( $script ) );
    }


    public function testDefer() : void {
        $script = new ScriptUri( 'URI' );
        $script->setDefer();
        /** @noinspection HtmlUnknownTarget */
        $st = '<script defer src="URI"></script>';
        self::assertSame( $st, strval( $script ) );
    }


}