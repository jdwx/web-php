<?php


declare( strict_types = 1 );


namespace Panels;


use JDWX\Web\Panels\ContainerPanel;
use JDWX\Web\Panels\PanelPage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Shims\MyBodyPanel;


#[CoversClass( ContainerPanel::class )]
final class ContainerPanelTest extends TestCase {


    public function testNested() : void {
        $panel1 = new MyBodyPanel();
        $panel1->stBody = 'Foo';
        $panel2 = new MyBodyPanel();
        $panel2->stBody = 'Bar';
        $cont1 = new ContainerPanel( [ $panel1, $panel2 ] );
        $panel3 = new MyBodyPanel();
        $panel3->stBody = 'Baz';
        $panel4 = new MyBodyPanel();
        $panel4->stBody = 'Qux';
        $cont2 = new ContainerPanel( [ $panel3, $panel4 ] );
        $page = new PanelPage( [ $cont1, $cont2 ] );
        $st = $page->render();
        self::assertStringContainsString( 'FooBarBazQux', $st );
    }


}
