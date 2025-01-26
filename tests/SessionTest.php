<?php


declare( strict_types = 1 );


use JDWX\Web\Backends\ISessionBackend;
use JDWX\Web\Backends\MockSessionBackend;
use JDWX\Web\Backends\PHPSessionBackend;
use JDWX\Web\Request;
use JDWX\Web\Session;
use PHPUnit\Framework\TestCase;


class SessionTest extends TestCase {


    public function testAbort() : void {
        $ses = $this->initSession();
        $ses->bFailAbort = true;
        static::expectException( RuntimeException::class );
        Session::abort();
    }


    public function testActive() : void {
        $this->initSession();
        static::assertFalse( Session::active() );
    }


    public function testCacheLimiter() : void {
        $this->initSession();
        static::assertEquals( 'nocache', Session::cacheLimiter() );
        Session::cacheLimiter( 'public' );
        static::assertEquals( 'public', Session::cacheLimiter() );
        static::expectException( InvalidArgumentException::class );
        Session::cacheLimiter( 'BLOWUP' );
    }


    public function testCacheLimiterForFailure() : void {
        $ses = $this->initSession();
        $ses->bFailCacheLimiter = true;
        static::expectException( RuntimeException::class );
        Session::cacheLimiter( 'public' );
    }


    public function testClear() : void {
        $this->initSession();
        Session::start();
        Session::set( 'foo', 'bar' );
        static::assertEquals( 'bar', Session::get( 'foo' ) );
        Session::clear( 'foo' );
        static::assertNull( Session::get( 'foo' ) );
    }


    public function testCookieInRequest() : void {
        $be = $this->initSession();
        $req = Request::synthetic( [], [], [], [] );
        static::assertFalse( Session::cookieInRequest( $req ) );
        $req = Request::synthetic( [], [], [ $be->stName => $be->stID ], [] );
        static::assertTrue( Session::cookieInRequest( $req ) );
        static::assertFalse( Session::cookieInRequest() );
    }


    public function testDefaultInstance() : void {
        $ses = new class() extends Session {


            public static function sessionCheck() : ISessionBackend {
                return static::backend();
            }


            public static function whack() : void {
                static::$backend = null;
            }


        };
        /** @noinspection PhpAccessStaticViaInstanceInspection */
        $ses::whack();
        /** @noinspection PhpAccessStaticViaInstanceInspection */
        $be = $ses::sessionCheck();
        static::assertInstanceOf( PHPSessionBackend::class, $be );
    }


    public function testDestroy() : void {
        $this->initSession();
        Session::start();
        Session::set( 'foo', 'bar' );
        static::assertEquals( 'bar', Session::get( 'foo' ) );
        Session::destroy();
        static::expectException( LogicException::class );
        Session::get( 'foo' );
    }


    public function testDestroyForFailure() : void {
        $ses = $this->initSession();
        $ses->bFailDestroy = true;
        Session::start();
        static::expectException( RuntimeException::class );
        Session::destroy();
    }


    public function testFlush() : void {
        $this->initSession();
        Session::start();
        Session::set( 'foo', 'bar' );
        $tmExpire = Session::get( 'tmExpire' );
        static::assertSame( 'bar', Session::get( 'foo' ) );
        Session::flush();
        static::assertNull( Session::get( 'foo' ) );
        static::assertSame( $tmExpire, Session::get( 'tmExpire' ) );
    }


    public function testGetInt() : void {
        $this->initSession();
        Session::start();
        Session::set( 'foo', 123 );
        static::assertSame( 123, Session::getInt( 'foo' ) );
        static::assertSame( 123, Session::getInt( 'foo', 456 ) );
        static::assertSame( 456, Session::getInt( 'bar', 456 ) );
        static::expectException( RuntimeException::class );
        Session::getInt( 'bar' );
    }


    public function testGetIntOrNull() : void {
        $this->initSession();
        Session::start();
        static::assertNull( Session::getIntOrNull( 'foo' ) );
        Session::set( 'foo', 123 );
        static::assertSame( 123, Session::getIntOrNull( 'foo' ) );
        Session::set( 'foo', 'bar' );
        static::expectException( TypeError::class );
        Session::getIntOrNull( 'foo' );
    }


    public function testGetString() : void {
        $this->initSession();
        Session::start();
        Session::set( 'foo', 'bar' );
        static::assertSame( 'bar', Session::getString( 'foo' ) );
        static::assertSame( 'baz', Session::getString( 'bar', 'baz' ) );
        static::expectException( RuntimeException::class );
        Session::getString( 'bar' );
    }


    public function testGetStringOrNull() : void {
        $this->initSession();
        Session::start();
        static::assertNull( Session::getStringOrNull( 'foo' ) );
        Session::set( 'foo', 'bar' );
        static::assertSame( 'bar', Session::getStringOrNull( 'foo' ) );
        Session::set( 'foo', 123 );
        static::expectException( TypeError::class );
        Session::getStringOrNull( 'foo' );
    }


    public function testId() : void {
        $ses = $this->initSession();
        Session::start();
        static::assertSame( 'test-id', Session::id() );
        $ses->bFailId = true;
        static::expectException( RuntimeException::class );
        Session::id();
    }


    public function testIncrement() : void {
        $this->initSession();
        Session::start();
        Session::set( 'foo', 123 );
        Session::increment( 'foo' );
        static::assertSame( 124, Session::getInt( 'foo' ) );
        Session::increment( 'bar', 5 );
        static::assertSame( 5, Session::getInt( 'bar' ) );
    }


    public function testList() : void {
        $this->initSession();
        Session::start();
        Session::clear( 'tmExpire' );
        Session::clear( 'tmStart' );
        Session::set( 'foo', 123 );
        Session::set( 'bar', 456 );
        $x = Session::list();
        static::assertSame( [ 'foo' => 123, 'bar' => 456 ], $x );
    }


    public function testNestedClear() : void {
        $this->initSession();
        Session::start();
        Session::nestedSet( 'foo', 'bar', 'baz' );
        Session::nestedSet( 'foo', 'qux', 'quux' );
        static::assertSame( 'baz', Session::nestedGet( 'foo', 'bar' ) );
        static::assertSame( 'quux', Session::nestedGet( 'foo', 'qux' ) );
        Session::nestedClear( 'foo', 'bar' );
        static::assertSame( 'quux', Session::nestedGet( 'foo', 'qux' ) );
        Session::nestedClear( 'foo', 'bar' );
        static::assertNull( Session::nestedGet( 'foo', 'bar' ) );
    }


    public function testNestedGetInt() : void {
        $this->initSession();
        Session::start();
        Session::nestedSet( 'foo', 'bar', 123 );
        static::assertSame( 123, Session::nestedGetInt( 'foo', 'bar' ) );
        static::assertSame( 123, Session::nestedGetInt( 'foo', 'bar', 456 ) );
        static::assertSame( 456, Session::nestedGetInt( 'foo', 'baz', 456 ) );
        static::expectException( RuntimeException::class );
        Session::nestedGetInt( 'foo', 'baz' );
    }


    public function testNestedGetIntOrNull() : void {
        $this->initSession();
        Session::start();
        static::assertNull( Session::nestedGetIntOrNull( 'foo', 'bar' ) );
        Session::nestedSet( 'foo', 'bar', 123 );
        static::assertSame( 123, Session::nestedGetIntOrNull( 'foo', 'bar' ) );
        Session::nestedSet( 'foo', 'bar', 'baz' );
        static::expectException( TypeError::class );
        Session::nestedGetIntOrNull( 'foo', 'bar' );
    }


    public function testNestedGetString() : void {
        $this->initSession();
        Session::start();
        Session::nestedSet( 'foo', 'bar', 'baz' );
        static::assertSame( 'baz', Session::nestedGetString( 'foo', 'bar' ) );
        static::assertSame( 'qux', Session::nestedGetString( 'foo', 'quux', 'qux' ) );
        static::expectException( RuntimeException::class );
        Session::nestedGetString( 'foo', 'quux' );
    }


    public function testNestedGetStringOrNull() : void {
        $this->initSession();
        Session::start();
        static::assertNull( Session::nestedGetStringOrNull( 'foo', 'bar' ) );
        Session::nestedSet( 'foo', 'bar', 'baz' );
        static::assertSame( 'baz', Session::nestedGetStringOrNull( 'foo', 'bar' ) );
        Session::nestedSet( 'foo', 'bar', 123 );
        static::expectException( TypeError::class );
        Session::nestedGetStringOrNull( 'foo', 'bar' );
    }


    public function testNestedIncrement() : void {
        $this->initSession();
        Session::start();
        Session::nestedSet( 'foo', 'bar', 123 );
        Session::nestedIncrement( 'foo', 'bar' );
        static::assertSame( 124, Session::nestedGetInt( 'foo', 'bar' ) );
        Session::nestedIncrement( 'foo', 'baz', 5 );
        static::assertSame( 5, Session::nestedGetInt( 'foo', 'baz' ) );
    }


    public function testPeek() : void {
        $this->initSession( [ 'foo' => 'bar', 'baz' => 'qux' ] );
        $peek = Session::peek();
        static::assertSame( [ 'foo' => 'bar', 'baz' => 'qux' ], $peek );
    }


    public function testRegenerate() : void {
        $ses = $this->initSession();
        Session::start();
        $stID = Session::id();
        Session::regenerate();
        static::assertNotSame( $stID, Session::id() );
        $ses->bFailRegenerate = true;
        static::expectException( RuntimeException::class );
        Session::regenerate();
    }


    public function testStart() : void {
        $be = $this->initSession();
        Session::start( i_stSessionName: 'alt-session-name' );
        static::assertSame( 'alt-session-name', $be->stName );
        Session::destroy();
        $be->bFailStart = true;
        static::expectException( RuntimeException::class );
        Session::start( i_stSessionName: 'alt-session-name-2' );
    }


    public function testStart2() : void {
        $this->initSession();
        $req = Request::synthetic( [], [], [ 'baz' => 'foo|bar' ], [] );
        static::assertFalse( Session::start( i_stSessionName: 'baz', i_req: $req ) );
        $req = Request::synthetic( [], [], [ 'baz' => str_repeat( '01234567890', 10 ) ], [] );
        static::assertFalse( Session::start( i_stSessionName: 'baz', i_req: $req ) );

        static::assertTrue( Session::start() );
        static::expectException( LogicException::class );
        Session::start();

    }


    public function testStart3() : void {
        $this->initSession( [ 'tmExpire' => time() - 3600, 'foo' => 'bar' ] );
        static::assertTrue( Session::start() );
        static::assertNull( Session::get( 'foo' ) );
    }


    public function testUnset() : void {
        $be = $this->initSession();
        Session::start();
        Session::set( 'foo', 'bar' );
        static::assertSame( 'bar', Session::get( 'foo' ) );
        Session::unset();
        static::assertFalse( Session::has( 'foo' ) );
        static::expectException( RuntimeException::class );
        $be->bFailUnset = true;
        Session::unset();
    }


    public function testWriteClose() : void {
        $this->initSession();
        Session::start();
        Session::set( 'foo', 'bar' );
        static::assertSame( 'bar', Session::get( 'foo' ) );
        Session::writeClose();
        static::expectException( LogicException::class );
        Session::get( 'foo' );
    }


    public function testWriteCloseForFailure() : void {
        $be = $this->initSession();
        Session::start();
        $be->bFailWriteClose = true;
        static::expectException( RuntimeException::class );
        Session::writeClose();
    }


    public function testWriteCloseForReadAfterClose() : void {
        $this->initSession();
        Session::start();
        Session::set( 'foo', 'bar' );
        static::assertSame( 'bar', Session::get( 'foo' ) );
        Session::writeClose();
        Session::start();
        static::assertSame( 'bar', Session::get( 'foo' ) );
    }


    /** @param array<string, mixed> $i_rSession */
    private function initSession( array $i_rSession = [] ) : MockSessionBackend {
        $be = new MockSessionBackend( $i_rSession );
        Session::init( $be );
        return $be;
    }


}
