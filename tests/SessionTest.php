<?php


declare( strict_types = 1 );


use JDWX\Web\Backends\MockSessionBackend;
use JDWX\Web\Backends\PHPSessionBackend;
use JDWX\Web\Backends\SessionBackendInterface;
use JDWX\Web\Request;
use JDWX\Web\Session;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( Session::class )]
final class SessionTest extends TestCase {


    public function testAbort() : void {
        $ses = $this->initSession();
        $ses->bFailAbort = true;
        self::expectException( RuntimeException::class );
        Session::abort();
    }


    public function testActive() : void {
        $this->initSession();
        self::assertFalse( Session::active() );
    }


    public function testCacheLimiter() : void {
        $this->initSession();
        self::assertEquals( 'nocache', Session::cacheLimiter() );
        Session::cacheLimiter( 'public' );
        self::assertEquals( 'public', Session::cacheLimiter() );
        self::expectException( InvalidArgumentException::class );
        Session::cacheLimiter( 'BLOWUP' );
    }


    public function testCacheLimiterForFailure() : void {
        $ses = $this->initSession();
        $ses->bFailCacheLimiter = true;
        self::expectException( RuntimeException::class );
        Session::cacheLimiter( 'public' );
    }


    /**
     * @noinspection PhpDeprecationInspection
     * @suppress PhanDeprecatedFunction
     */
    public function testClear() : void {
        $this->initSession();
        Session::start();
        Session::set( 'foo', 'bar' );
        self::assertEquals( 'bar', Session::get( 'foo' ) );
        Session::clear( 'foo' );
        self::assertNull( Session::get( 'foo' ) );
    }


    public function testCookieInRequest() : void {
        $be = $this->initSession();
        $req = Request::synthetic( [], [], [], [] );
        self::assertFalse( Session::cookieInRequest( $req ) );
        $req = Request::synthetic( [], [], [ $be->stName => $be->stID ], [] );
        self::assertTrue( Session::cookieInRequest( $req ) );
        self::assertFalse( Session::cookieInRequest() );
    }


    public function testDefaultInstance() : void {
        $ses = new class() extends Session {


            public static function sessionCheck() : SessionBackendInterface {
                return self::backend();
            }


            public static function whack() : void {
                self::$backend = null;
            }


        };
        /** @noinspection PhpAccessStaticViaInstanceInspection */
        $ses::whack();
        /** @noinspection PhpAccessStaticViaInstanceInspection */
        $be = $ses::sessionCheck();
        self::assertInstanceOf( PHPSessionBackend::class, $be );
    }


    public function testDestroy() : void {
        $this->initSession();
        Session::start();
        Session::set( 'foo', 'bar' );
        self::assertEquals( 'bar', Session::get( 'foo' ) );
        Session::destroy();
        self::expectException( LogicException::class );
        Session::get( 'foo' );
    }


    public function testDestroyForFailure() : void {
        $ses = $this->initSession();
        $ses->bFailDestroy = true;
        Session::start();
        self::expectException( RuntimeException::class );
        Session::destroy();
    }


    public function testFlush() : void {
        $this->initSession();
        Session::start();
        Session::set( 'foo', 'bar' );
        $tmExpire = Session::get( 'tmExpire' );
        self::assertSame( 'bar', Session::get( 'foo' ) );
        Session::flush();
        self::assertNull( Session::get( 'foo' ) );
        self::assertSame( $tmExpire, Session::get( 'tmExpire' ) );
    }


    public function testGet() : void {
        $this->initSession();
        Session::start();
        Session::set( 'foo', 'bar' );
        self::assertSame( 'bar', Session::get( 'foo' ) );
        self::assertSame( 'baz', Session::get( 'qux', 'baz' ) );
    }


    public function testGetInt() : void {
        $this->initSession();
        Session::start();
        Session::set( 'foo', 123 );
        self::assertSame( 123, Session::getInt( 'foo' ) );
        self::assertSame( 123, Session::getInt( 'foo', 456 ) );
        self::assertSame( 456, Session::getInt( 'bar', 456 ) );
        self::expectException( RuntimeException::class );
        Session::getInt( 'bar' );
    }


    public function testGetIntOrNull() : void {
        $this->initSession();
        Session::start();
        self::assertNull( Session::getIntOrNull( 'foo' ) );
        Session::set( 'foo', 123 );
        self::assertSame( 123, Session::getIntOrNull( 'foo' ) );
        Session::set( 'foo', 'bar' );
        self::expectException( TypeError::class );
        Session::getIntOrNull( 'foo' );
    }


    public function testGetString() : void {
        $this->initSession();
        Session::start();
        Session::set( 'foo', 'bar' );
        self::assertSame( 'bar', Session::getString( 'foo' ) );
        self::assertSame( 'baz', Session::getString( 'bar', 'baz' ) );
        self::expectException( RuntimeException::class );
        Session::getString( 'bar' );
    }


    public function testGetStringOrNull() : void {
        $this->initSession();
        Session::start();
        self::assertNull( Session::getStringOrNull( 'foo' ) );
        Session::set( 'foo', 'bar' );
        self::assertSame( 'bar', Session::getStringOrNull( 'foo' ) );
        Session::set( 'foo', 123 );
        self::expectException( TypeError::class );
        Session::getStringOrNull( 'foo' );
    }


    public function testId() : void {
        $ses = $this->initSession();
        Session::start();
        self::assertSame( 'test-id', Session::id() );
        $ses->bFailId = true;
        self::expectException( RuntimeException::class );
        Session::id();
    }


    public function testIncrement() : void {
        $this->initSession();
        Session::start();
        Session::set( 'foo', 123 );
        Session::increment( 'foo' );
        self::assertSame( 124, Session::getInt( 'foo' ) );
        Session::increment( 'bar', 5 );
        self::assertSame( 5, Session::getInt( 'bar' ) );
    }


    public function testList() : void {
        $this->initSession();
        Session::start();
        Session::remove( 'tmExpire' );
        Session::remove( 'tmStart' );
        Session::set( 'foo', 123 );
        Session::set( 'bar', 456 );
        $x = Session::list();
        self::assertSame( [ 'foo' => 123, 'bar' => 456 ], $x );
    }


    /**
     * @noinspection PhpDeprecationInspection
     * @suppress PhanDeprecatedFunction
     */
    public function testNestedClear() : void {
        $this->initSession();
        Session::start();
        Session::nestedSet( 'foo', 'bar', 'baz' );
        Session::nestedSet( 'foo', 'qux', 'quux' );
        self::assertSame( 'baz', Session::nestedGet( 'foo', 'bar' ) );
        self::assertSame( 'quux', Session::nestedGet( 'foo', 'qux' ) );
        Session::nestedClear( 'foo', 'bar' );
        self::assertSame( 'quux', Session::nestedGet( 'foo', 'qux' ) );
        Session::nestedClear( 'foo', 'bar' );
        self::assertNull( Session::nestedGet( 'foo', 'bar' ) );
    }


    public function testNestedGetInt() : void {
        $this->initSession();
        Session::start();
        Session::nestedSet( 'foo', 'bar', 123 );
        self::assertSame( 123, Session::nestedGetInt( 'foo', 'bar' ) );
        self::assertSame( 123, Session::nestedGetInt( 'foo', 'bar', 456 ) );
        self::assertSame( 456, Session::nestedGetInt( 'foo', 'baz', 456 ) );
        self::expectException( RuntimeException::class );
        Session::nestedGetInt( 'foo', 'baz' );
    }


    public function testNestedGetIntOrNull() : void {
        $this->initSession();
        Session::start();
        self::assertNull( Session::nestedGetIntOrNull( 'foo', 'bar' ) );
        Session::nestedSet( 'foo', 'bar', 123 );
        self::assertSame( 123, Session::nestedGetIntOrNull( 'foo', 'bar' ) );
        Session::nestedSet( 'foo', 'bar', 'baz' );
        self::expectException( TypeError::class );
        Session::nestedGetIntOrNull( 'foo', 'bar' );
    }


    public function testNestedGetString() : void {
        $this->initSession();
        Session::start();
        Session::nestedSet( 'foo', 'bar', 'baz' );
        self::assertSame( 'baz', Session::nestedGetString( 'foo', 'bar' ) );
        self::assertSame( 'qux', Session::nestedGetString( 'foo', 'quux', 'qux' ) );
        self::expectException( RuntimeException::class );
        Session::nestedGetString( 'foo', 'quux' );
    }


    public function testNestedGetStringOrNull() : void {
        $this->initSession();
        Session::start();
        self::assertNull( Session::nestedGetStringOrNull( 'foo', 'bar' ) );
        Session::nestedSet( 'foo', 'bar', 'baz' );
        self::assertSame( 'baz', Session::nestedGetStringOrNull( 'foo', 'bar' ) );
        Session::nestedSet( 'foo', 'bar', 123 );
        self::expectException( TypeError::class );
        Session::nestedGetStringOrNull( 'foo', 'bar' );
    }


    public function testNestedIncrement() : void {
        $this->initSession();
        Session::start();
        Session::nestedSet( 'foo', 'bar', 123 );
        Session::nestedIncrement( 'foo', 'bar' );
        self::assertSame( 124, Session::nestedGetInt( 'foo', 'bar' ) );
        Session::nestedIncrement( 'foo', 'baz', 5 );
        self::assertSame( 5, Session::nestedGetInt( 'foo', 'baz' ) );
    }


    public function testNestedRemove() : void {
        $be = $this->initSession();
        Session::start();
        $be->set( 'foo', [ 'bar' => 'baz', 'qux' => 'quux' ] );
        Session::nestedRemove( 'foo', 'bar' );
        Session::nestedRemove( 'foo', 'corge' );
        self::assertSame( [ 'qux' => 'quux' ], $be->get( 'foo' ) );
    }


    public function testPeek() : void {
        $this->initSession( [ 'foo' => 'bar', 'baz' => 'qux' ] );
        $peek = Session::peek();
        self::assertSame( [ 'foo' => 'bar', 'baz' => 'qux' ], $peek );
    }


    public function testRegenerate() : void {
        $ses = $this->initSession();
        Session::start();
        $stID = Session::id();
        Session::regenerate();
        self::assertNotSame( $stID, Session::id() );
        $ses->bFailRegenerate = true;
        self::expectException( RuntimeException::class );
        Session::regenerate();
    }


    public function testRemove() : void {
        $be = $this->initSession();
        Session::start();
        $be->set( 'foo', 'bar' );
        $be->set( 'qux', 'quux' );
        Session::remove( 'foo' );
        self::assertFalse( $be->has( 'foo' ) );
        Session::remove( 'baz' );
        self::assertFalse( $be->has( 'baz' ) );
        self::assertTrue( $be->has( 'qux' ) );
    }


    public function testReset() : void {
        $tmExpire = time() + 60;
        $this->initSession( [ 'foo' => 'bar', 'tmExpire' => $tmExpire ] );
        Session::start();
        self::assertSame( 'bar', Session::get( 'foo' ) );
        Session::set( 'foo', 'baz' );
        self::assertSame( 'baz', Session::get( 'foo' ) );
        $tmExpire2 = Session::getInt( 'tmExpire' );
        Session::reset();
        self::assertSame( $tmExpire2, Session::getInt( 'tmExpire' ) );
        Session::reset( false );
        self::assertSame( $tmExpire, Session::getInt( 'tmExpire' ) );
        self::assertSame( 'bar', Session::get( 'foo' ) );
    }


    public function testResetForFailure() : void {
        $be = $this->initSession();
        $be->bFailReset = true;
        Session::start();
        self::expectException( RuntimeException::class );
        Session::reset();
    }


    public function testResetForNotStarted() : void {
        $this->initSession();
        self::expectException( LogicException::class );
        Session::reset();
    }


    public function testSoftStart() : void {
        $be = $this->initSession();
        self::assertTrue( Session::softStart() );
        self::assertTrue( $be->bActive );
        self::assertTrue( Session::softStart() );
    }


    public function testStart() : void {
        $be = $this->initSession();
        Session::start( i_stSessionName: 'alt-session-name' );
        self::assertSame( 'alt-session-name', $be->stName );
        Session::destroy();
        $be->bFailStart = true;
        self::expectException( RuntimeException::class );
        Session::start( i_stSessionName: 'alt-session-name-2' );
    }


    public function testStart2() : void {
        $this->initSession();
        $req = Request::synthetic( [], [], [ 'baz' => 'foo|bar' ], [] );
        self::assertFalse( Session::start( i_stSessionName: 'baz', i_req: $req ) );
        $req = Request::synthetic( [], [], [ 'baz' => str_repeat( '01234567890', 10 ) ], [] );
        self::assertFalse( Session::start( i_stSessionName: 'baz', i_req: $req ) );

        self::assertTrue( Session::start() );
        self::expectException( LogicException::class );
        Session::start();

    }


    public function testStart3() : void {
        $this->initSession( [ 'tmExpire' => time() - 3600, 'foo' => 'bar' ] );
        self::assertTrue( Session::start() );
        self::assertNull( Session::get( 'foo' ) );
    }


    public function testUnset() : void {
        $be = $this->initSession();
        Session::start();
        Session::set( 'foo', 'bar' );
        self::assertSame( 'bar', Session::get( 'foo' ) );
        Session::unset();
        self::assertFalse( Session::has( 'foo' ) );
        self::expectException( RuntimeException::class );
        $be->bFailUnset = true;
        Session::unset();
    }


    public function testWriteClose() : void {
        $this->initSession();
        Session::start();
        Session::set( 'foo', 'bar' );
        self::assertSame( 'bar', Session::get( 'foo' ) );
        Session::writeClose();
        self::expectException( LogicException::class );
        Session::get( 'foo' );
    }


    public function testWriteCloseForFailure() : void {
        $be = $this->initSession();
        Session::start();
        $be->bFailWriteClose = true;
        self::expectException( RuntimeException::class );
        Session::writeClose();
    }


    public function testWriteCloseForReadAfterClose() : void {
        $this->initSession();
        Session::start();
        Session::set( 'foo', 'bar' );
        self::assertSame( 'bar', Session::get( 'foo' ) );
        Session::writeClose();
        Session::start();
        self::assertSame( 'bar', Session::get( 'foo' ) );
    }


    /** @param array<string, mixed> $i_rSession */
    private function initSession( array $i_rSession = [] ) : MockSessionBackend {
        $be = new MockSessionBackend( $i_rSession );
        Session::init( $be );
        return $be;
    }


}
