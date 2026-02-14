<?php /** @noinspection UnnecessaryAssertionInspection */


declare( strict_types = 1 );


namespace JDWX\Web\Tests;


use JDWX\Web\Backends\MockSessionBackend;
use JDWX\Web\SessionControl;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( SessionControl::class )]
final class SessionControlTest extends TestCase {


    public function testGet() : void {
        self::assertInstanceOf( SessionControl::class, SessionControl::get() );

        $x = new SessionControl();
        SessionControl::set( $x );
        self::assertSame( $x, SessionControl::get() );

    }


    public function testNamespace() : void {
        $be = new MockSessionBackend( [ 'foo' => 'bar', 'baz' => [ 'foo' => 'qux' ] ] );
        $be->start();
        $sc = new SessionControl( $be );
        $ns = $sc->namespace();
        self::assertSame( 'bar', $ns->get( 'foo' ) );
    }


    public function testSetForBackend() : void {
        $x = new MockSessionBackend( [] );
        SessionControl::set( $x, 12345 );
        $y = SessionControl::get();
        self::assertSame( $x, $y->backend() );
        self::assertSame( 12345, $y->lifetime() );
    }


    public function testSetForSessionControl() : void {
        $x = new SessionControl();
        SessionControl::set( $x );
        self::assertSame( $x, SessionControl::get() );


    }


}
