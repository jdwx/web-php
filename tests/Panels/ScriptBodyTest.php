<?php


declare( strict_types = 1 );


namespace Panels;


use JDWX\Web\Panels\ScriptBody;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( ScriptBody::class )]
final class ScriptBodyTest extends TestCase {


    public function testBody() : void {
        $script = new ScriptBody( 'let foo = "bar"' );
        self::assertSame( '<script>let foo = "bar"</script>', strval( $script ) );
    }


}