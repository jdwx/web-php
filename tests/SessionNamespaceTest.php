<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests;


use JDWX\Web\Backends\MockSessionBackend;
use JDWX\Web\SessionControl;
use JDWX\Web\SessionNamespace;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;


#[CoversClass( SessionNamespace::class )]
final class SessionNamespaceTest extends TestCase {


    public function testClear() : void {
        $be = new MockSessionBackend( [
            'foo' => 'bar',
            'baz' => [
                'foo' => 'qux',
            ],
        ] );
        $be->start();
        $sns = new SessionNamespace( $be );
        $sns2 = $sns->namespace( 'baz' );
        $sns2->clear();
        self::assertSame( [ 'foo' => 'bar', 'baz' => [] ], $be->rSession );
    }


    public function testClearForBase() : void {
        $be = new MockSessionBackend( [
            'foo' => 'bar',
            'baz' => [
                'foo' => 'qux',
            ],
        ] );
        $be->start();
        $ns = new SessionNamespace( $be );
        $ns->clear();
        self::assertSame( [], $be->rSession );
    }


    public function testConstructForString() : void {
        $be = new MockSessionBackend( [
            'foo' => 'bar',
            'baz' => [
                'foo' => 'qux',
            ],
        ] );
        $be->start();
        $ns = new SessionNamespace( $be, 'baz' );
        self::assertSame( 'qux', $ns->get( 'foo' ) );
    }


    public function testDefault() : void {
        $be = new MockSessionBackend( [
            'foo' => 'bar',
            'baz' => [
                'foo' => 'qux',
            ],
        ] );
        $be->start();
        SessionControl::setGlobal( $be );
        $ns = SessionNamespace::default( 'baz' );
        self::assertSame( $be, $ns->backend() );
        self::assertSame( 'qux', $ns->get( 'foo' ) );
    }


    public function testGet() : void {
        $be = new MockSessionBackend( [
            'foo' => 'bar',
            'baz' => [
                'foo' => 'qux',
            ],
        ] );
        $be->start();
        $ns = new SessionNamespace( $be );
        self::assertSame( 'bar', $ns->get( 'foo' ) );
        $ns2 = $ns->namespace( [ 'baz' ] );
        self::assertSame( 'qux', $ns2->get( 'foo' ) );
        self::assertNull( $ns->get( 'nonexistent' ) );
        self::assertSame( 'default', $ns->get( 'nonexistent', 'default' ) );
    }


    public function testGetInt() : void {
        $be = new MockSessionBackend( [
            'foo' => 42,
            'baz' => [
                'foo' => 84,
                'qux' => 'not an int',
            ],
        ] );
        $be->start();
        $ns = new SessionNamespace( $be );
        self::assertSame( 42, $ns->getInt( 'foo' ) );
        $ns2 = $ns->namespace( [ 'baz' ] );
        self::assertSame( 84, $ns2->getInt( 'foo' ) );
        self::assertSame( 0, $ns->getInt( 'nonexistent', 0 ) );
        $this->expectException( RuntimeException::class );
        $ns->getInt( 'nonexistent' );
    }


    public function testGetIntOrNull() : void {
        $be = new MockSessionBackend( [
            'foo' => 42,
            'baz' => [
                'foo' => 84,
                'qux' => 'not an int',
            ],
        ] );
        $be->start();
        $ns = new SessionNamespace( $be );
        self::assertSame( 42, $ns->getIntOrNull( 'foo' ) );
        $ns2 = $ns->namespace( [ 'baz' ] );
        self::assertSame( 84, $ns2->getIntOrNull( 'foo' ) );
        self::assertNull( $ns->getIntOrNull( 'nonexistent' ) );
        $this->expectException( RuntimeException::class );
        $ns2->getIntOrNull( 'qux' );
    }


    public function testGetString() : void {
        $be = new MockSessionBackend( [
            'foo' => 'bar',
            'baz' => [
                'foo' => 'qux',
            ],
        ] );
        $be->start();
        $ns = new SessionNamespace( $be );
        self::assertSame( 'bar', $ns->getString( 'foo' ) );
        $ns2 = $ns->namespace( [ 'baz' ] );
        self::assertSame( 'qux', $ns2->getString( 'foo' ) );
        self::assertSame( 'default', $ns->getString( 'nonexistent', 'default' ) );
        $this->expectException( RuntimeException::class );
        $ns->getString( 'nonexistent' );
    }


    public function testGetStringOrNull() : void {
        $be = new MockSessionBackend( [
            'foo' => 'bar',
            'baz' => [
                'foo' => 'qux',
                'quux' => 123, // Not a string
            ],
        ] );
        $be->start();
        $ns = new SessionNamespace( $be );
        self::assertSame( 'bar', $ns->getStringOrNull( 'foo' ) );
        $ns2 = $ns->namespace( [ 'baz' ] );
        self::assertSame( 'qux', $ns2->getStringOrNull( 'foo' ) );
        self::assertNull( $ns->getStringOrNull( 'nonexistent' ) );
        $this->expectException( RuntimeException::class );
        $ns2->getStringOrNull( 'quux' );
    }


    public function testIncrement() : void {
        $be = new MockSessionBackend( [
            'foo' => 1,
            'baz' => [
                'foo' => 2,
            ],
        ] );
        $be->start();
        $ns = new SessionNamespace( $be );
        self::assertSame( 1, $ns->getInt( 'foo' ) );
        $ns->increment( 'foo' );
        self::assertSame( 2, $ns->getInt( 'foo' ) );
        $ns->increment( 'foo', 3 );
        self::assertSame( 5, $ns->getInt( 'foo' ) );
        $ns->increment( 'bar', 10 );
        self::assertSame( 10, $ns->getInt( 'bar' ) );
    }


    public function testList() : void {
        $be = new MockSessionBackend( [
            'foo' => 'bar',
            'baz' => [
                'foo' => 'qux',
            ],
        ] );
        $be->start();
        $ns = new SessionNamespace( $be );
        self::assertSame( [ 'foo' => 'bar', 'baz' => [ 'foo' => 'qux' ] ], $ns->list() );
        $ns2 = $ns->namespace( [ 'baz' ] );
        self::assertSame( [ 'foo' => 'qux' ], $ns2->list() );
    }


    public function testNamespace() : void {
        $be = new MockSessionBackend( [
            'foo' => 'bar',
            'baz' => [
                'foo' => 'qux',
            ],
            'quux' => [
                'foo' => 'corge',
            ],
        ] );
        $be->start();
        $ns = new SessionNamespace( $be );
        self::assertSame( 'bar', $ns->get( 'foo' ) );
        $ns2 = $ns->namespace( [ 'baz' ] );
        self::assertSame( 'qux', $ns2->get( 'foo' ) );
        $ns2 = $ns->namespace( 'quux' );
        self::assertSame( 'corge', $ns2->get( 'foo' ) );
        $this->expectException( RuntimeException::class );
        $ns->namespace( 'foo' );
    }


    public function testRemove() : void {
        $be = new MockSessionBackend( [
            'foo' => 'bar',
            'baz' => [
                'foo' => 'qux',
            ],
        ] );
        $be->start();
        $ns = new SessionNamespace( $be );
        $ns->remove( 'foo' );
        self::assertNull( $ns->get( 'foo' ) );
        $ns2 = $ns->namespace( [ 'baz' ] );
        $ns2->remove( 'foo' );
        self::assertNull( $ns2->get( 'foo' ) );
        self::assertSame( [ 'baz' => [] ], $be->rSession );
    }


    public function testSet() : void {
        $be = new MockSessionBackend( [] );
        $be->start();
        $ns = new SessionNamespace( $be );
        $ns->set( 'foo', 'bar' );
        self::assertSame( 'bar', $ns->get( 'foo' ) );
        $ns2 = $ns->namespace( [ 'baz' ] );
        $ns2->set( 'foo', 'qux' );
        self::assertSame( 'qux', $ns2->get( 'foo' ) );
        self::assertSame( [ 'foo' => 'bar', 'baz' => [ 'foo' => 'qux' ] ], $be->rSession );
    }


}
