<?php


declare( strict_types = 1 );


namespace Panels;


use JDWX\Web\Panels\ScriptBody;
use JDWX\Web\Panels\ScriptListTrait;
use JDWX\Web\Panels\ScriptUri;
use PHPUnit\Framework\TestCase;


final class ScriptListTest extends TestCase {


    public function testAddScript() : void {
        $obj = $this->newObject();
        $script = new ScriptBody( 'test' );
        $obj->addScript( $script );
        self::assertSame( [ $script ], iterator_to_array( $obj->scriptList() ) );
        $script2 = new ScriptUri( 'test2' );
        $obj->addScript( $script2 );
        self::assertSame(
            [ $script, $script2 ],
            iterator_to_array( $obj->scriptList() )
        );
    }


    public function testAddScriptBody() : void {
        $obj = $this->newObject();
        $obj->addScriptBody( 'let bar = null' );
        $st = $obj->text();
        self::assertSame( '<script>let bar = null</script>', $st );
    }


    public function testAddScriptUri() : void {
        $obj = $this->newObject();
        $obj->addScriptUri( 'foo' );
        $st = $obj->text();
        /** @noinspection HtmlUnknownTarget */
        self::assertSame( '<script src="foo"></script>', $st );
    }


    private function newObject() : object {
        return new class() {


            use ScriptListTrait;


            public function text() : string {
                $st = '';
                /** @noinspection PhpLoopCanBeReplacedWithImplodeInspection */
                foreach ( $this->scriptList() as $script ) {
                    $st .= $script;
                }
                return $st;
            }


        };
    }


}
