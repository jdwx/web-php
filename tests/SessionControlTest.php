<?php /** @noinspection UnnecessaryAssertionInspection */


declare( strict_types = 1 );


namespace JDWX\Web\Tests;


use JDWX\Web\Backends\MockSessionBackend;
use JDWX\Web\SessionControl;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( SessionControl::class )]
final class SessionControlTest extends TestCase {


    public function testGetGlobal() : void {
        self::assertInstanceOf( SessionControl::class, SessionControl::getGlobal() );

        $x = new SessionControl();
        SessionControl::setGlobal( $x );
        self::assertSame( $x, SessionControl::getGlobal() );

    }


    public function testNamespace() : void {
        $be = new MockSessionBackend( [ 'foo' => 'bar', 'baz' => [ 'foo' => 'qux' ] ] );
        $be->start();
        $sc = new SessionControl( $be );
        $ns = $sc->namespace();
        self::assertSame( 'bar', $ns->get( 'foo' ) );
    }


    public function testSetGlobalForBackend() : void {
        $x = new MockSessionBackend( [] );
        SessionControl::setGlobal( $x, 12345 );
        $y = SessionControl::getGlobal();
        self::assertSame( $x, $y->backend() );
        self::assertSame( 12345, $y->lifetime() );
    }


    public function testSetGlobalForSessionControl() : void {
        $x = new SessionControl();
        SessionControl::setGlobal( $x );
        self::assertSame( $x, SessionControl::getGlobal() );


    }


}
