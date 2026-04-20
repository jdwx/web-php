<?php /** @noinspection UnnecessaryAssertionInspection */


declare( strict_types = 1 );


namespace JDWX\Web\Tests;


use JDWX\Web\Backends\MockSessionBackend;
use JDWX\Web\Request;
use JDWX\Web\SessionControl;
use JDWX\Web\Tests\Shims\MyRequest;
use LogicException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;


#[CoversClass( SessionControl::class )]
final class SessionControlTest extends TestCase {


    public function testExpires() : void {
        $be = new MockSessionBackend( [] );
        $be->start();
        $sc = new SessionControl( $be );

        # No tmExpire set yet — should return null.
        self::assertNull( $sc->expires() );

        # Set tmExpire and verify it is returned.
        $tmExpire = time() + 3600;
        $sc->namespace()->set( 'tmExpire', $tmExpire );
        self::assertSame( $tmExpire, $sc->expires() );
    }


    public function testExpiresEx() : void {
        $be = new MockSessionBackend( [] );
        $be->start();
        $sc = new SessionControl( $be );

        # Set tmExpire and verify it is returned.
        $tmExpire = time() + 3600;
        $sc->namespace()->set( 'tmExpire', $tmExpire );
        self::assertSame( $tmExpire, $sc->expiresEx() );
    }


    public function testExpiresExThrowsWhenNotSet() : void {
        $be = new MockSessionBackend( [] );
        $be->start();
        $sc = new SessionControl( $be );

        # No tmExpire set — should throw.
        $this->expectException( RuntimeException::class );
        $sc->expiresEx();
    }


    public function testGetGlobal() : void {
        self::assertInstanceOf( SessionControl::class, SessionControl::getGlobal() );
        $sc = new SessionControl();
        SessionControl::setGlobal( $sc );
        self::assertSame( $sc, SessionControl::getGlobal() );
    }


    public function testIsExpired() : void {
        $be = new MockSessionBackend( [
            'tmExpire' => time() + 3600,
        ] );
        $be->start();
        $sc = new SessionControl( $be );
        self::assertFalse( $sc->isExpired() );
    }


    public function testIsExpiredForExpired() : void {
        $be = new MockSessionBackend( [
            'tmExpire' => time() - 3600,
        ] );
        $be->start();
        $sc = new SessionControl( $be );
        self::assertTrue( $sc->isExpired() );
    }


    public function testIsExpiredForNotStarted() : void {
        $be = new MockSessionBackend( [] );
        $sc = new SessionControl( $be );
        $this->expectException( LogicException::class );
        $sc->isExpired();
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


    public function testStartAppliesStrictModeAndSidLength() : void {
        $be = new MockSessionBackend( [] );
        # Use an explicit sid length so the test doesn't depend on the default.
        $sc = new SessionControl( $be, null, 32 );
        $req = Request::synthetic( null, null, [], null );

        self::assertTrue( $sc->start( null, null, $req ) );
        self::assertArrayHasKey( 'use_strict_mode', $be->rStartOptions );
        self::assertTrue( $be->rStartOptions[ 'use_strict_mode' ] );
        self::assertArrayHasKey( 'sid_length', $be->rStartOptions );
        self::assertSame( 32, $be->rStartOptions[ 'sid_length' ] );
        MyRequest::whackGlobal();
    }


    public function testStartAppliesCookieFlags() : void {
        $be = new MockSessionBackend( [] );
        $sc = new SessionControl( $be );
        $req = Request::synthetic( null, null, [], null );

        self::assertTrue( $sc->start( null, null, $req ) );
        self::assertSame( [
            'httponly' => true,
            'secure' => true,
            'samesite' => 'Strict',
        ], $be->rCookieParams );
        MyRequest::whackGlobal();
    }


    public function testStartAllowsDisablingFlags() : void {
        $be = new MockSessionBackend( [] );
        $sc = new SessionControl( $be );
        $req = Request::synthetic( null, null, [], null );

        self::assertTrue( $sc->start( null, null, $req, false, false, false, null ) );
        self::assertArrayNotHasKey( 'use_strict_mode', $be->rStartOptions );
        self::assertSame( [], $be->rCookieParams );
        MyRequest::whackGlobal();
    }


    public function testStartAllowsCustomSameSite() : void {
        $be = new MockSessionBackend( [] );
        $sc = new SessionControl( $be );
        $req = Request::synthetic( null, null, [], null );

        self::assertTrue( $sc->start( null, null, $req, true, true, true, 'Lax' ) );
        self::assertSame( 'Lax', $be->rCookieParams[ 'samesite' ] );
        MyRequest::whackGlobal();
    }


    public function testStartRejectsWrongLengthSid() : void {
        $be = new MockSessionBackend( [] );
        $sc = new SessionControl( $be, null, 32 );
        # Valid characters, wrong length (31 instead of 32).
        $req = Request::synthetic( null, null, [ 'test-session' => str_repeat( 'a', 31 ) ], null );

        self::assertFalse( $sc->start( null, null, $req ) );
        self::assertFalse( $be->bActive );
        MyRequest::whackGlobal();
    }


    public function testStartRejectsBogusChars() : void {
        $be = new MockSessionBackend( [] );
        $sc = new SessionControl( $be, null, 32 );
        # Correct length but contains a character outside [-a-zA-Z0-9,].
        $req = Request::synthetic( null, null, [ 'test-session' => str_repeat( 'a', 31 ) . '!' ], null );

        self::assertFalse( $sc->start( null, null, $req ) );
        self::assertFalse( $be->bActive );
        MyRequest::whackGlobal();
    }


    public function testStartAcceptsValidSid() : void {
        $be = new MockSessionBackend( [] );
        $sc = new SessionControl( $be, null, 32 );
        $stSid = str_repeat( 'a', 32 );
        $req = Request::synthetic( null, null, [ 'test-session' => $stSid ], null );

        self::assertTrue( $sc->start( null, null, $req ) );
        self::assertTrue( $be->bActive );
        MyRequest::whackGlobal();
    }


    public function testStartUsesCustomSidLength() : void {
        $be = new MockSessionBackend( [] );
        $sc = new SessionControl( $be, null, 48 );
        # Length 32 should now be rejected because the constructor overrode the expected length to 48.
        $req = Request::synthetic( null, null, [ 'test-session' => str_repeat( 'a', 32 ) ], null );

        self::assertFalse( $sc->start( null, null, $req ) );
        self::assertFalse( $be->bActive );

        # And the correct length succeeds, with sid_length propagated to the backend.
        $req = Request::synthetic( null, null, [ 'test-session' => str_repeat( 'a', 48 ) ], null );
        self::assertTrue( $sc->start( null, null, $req ) );
        self::assertSame( 48, $be->rStartOptions[ 'sid_length' ] );
        MyRequest::whackGlobal();
    }


    public function testStartTime() : void {
        $tm = time() - 3600;
        $be = new MockSessionBackend( [ 'tmStart' => $tm ] );
        $be->start();
        $sc = new SessionControl( $be );
        self::assertSame( $tm, $sc->startTime() );
    }


    public function testStartTimeEx() : void {
        $tm = time() - 3600;
        $be = new MockSessionBackend( [ 'tmStart' => $tm ] );
        $be->start();
        $sc = new SessionControl( $be );
        self::assertSame( $tm, $sc->startTimeEx() );
    }


    public function testStartTimeExForMissing() : void {
        $be = new MockSessionBackend( [] );
        $be->start();
        $sc = new SessionControl( $be );
        $this->expectException( RuntimeException::class );
        $sc->startTimeEx();
    }


    public function testStartTimeExForNotStarted() : void {
        $be = new MockSessionBackend( [] );
        $sc = new SessionControl( $be );
        $this->expectException( LogicException::class );
        $sc->startTimeEx();
    }


    public function testStartTimeForMissing() : void {
        $be = new MockSessionBackend( [] );
        $be->start();
        $sc = new SessionControl( $be );
        self::assertNull( $sc->startTime() );
    }


    public function testStartTimeForNotStarted() : void {
        $be = new MockSessionBackend( [] );
        $sc = new SessionControl( $be );
        $this->expectException( LogicException::class );
        $sc->startTime();
    }


}
