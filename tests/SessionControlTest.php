<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests;


use JDWX\Web\Backends\MockSessionBackend;
use JDWX\Web\SessionControl;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( SessionControl::class )]
final class SessionControlTest extends TestCase {


    public function testNamespace() : void {
        $be = new MockSessionBackend( [ 'foo' => 'bar', 'baz' => [ 'foo' => 'qux' ] ] );
        $be->start();
        $sc = new SessionControl( $be );
        $ns = $sc->namespace();
        self::assertSame( 'bar', $ns->get( 'foo' ) );

    }


}
