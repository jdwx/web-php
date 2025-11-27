<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Backends;


use JDWX\Strict\Exceptions\TypeException;
use JDWX\Web\Backends\AbstractSessionBackend;
use JDWX\Web\Backends\MockSessionBackend;
use LogicException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( AbstractSessionBackend::class )]
#[CoversClass( MockSessionBackend::class )]
class MockSessionBackendTest extends TestCase {


    public function testGetForBadNamespace() : void {
        $be = new MockSessionBackend( [ 'foo' => 'bar' ] );
        $this->expectException( LogicException::class );
        $be->get( [ 'foo' ], 'bar' );
    }


    public function testHasNamespace() : void {
        $be = new MockSessionBackend( [
            'foo' => [
                'bar' => [
                    'baz' => 'qux',
                ],
            ],
        ] );
        self::assertTrue( $be->hasNamespace( [], [ 'foo', 'bar' ] ) );
        self::assertTrue( $be->hasNamespace( [ 'foo' ], [ 'bar' ] ) );
        self::assertFalse( $be->hasNamespace( [ 'foo' ], [ 'bar', 'baz' ] ) );
        self::assertTrue( $be->hasNamespace( [ 'foo' ], [ 'bar', 'quux' ] ) );
    }


    public function testId() : void {
        $backend = new MockSessionBackend( [] );
        $backend->id( 'FooBar' );
        $backend->start();
        self::assertSame( 'FooBar', $backend->id() );
        $this->expectException( LogicException::class );
        $backend->id( 'BazQux' );
    }


    public function testName() : void {
        $backend = new MockSessionBackend( [] );
        self::assertSame( 'test-session', $backend->name() );

        $backend->bstName = false;
        self::assertFalse( $backend->name() );
    }


    public function testNameEx() : void {
        $backend = new MockSessionBackend( [] );
        self::assertSame( 'test-session', $backend->nameEx() );

        $backend->bstName = false;
        $this->expectException( TypeException::class );
        $backend->nameEx();
    }


    public function testReset() : void {
        $backend = new MockSessionBackend( [] );
        self::assertFalse( $backend->reset() );
        $backend->start();
        $backend->rSession = [ 'foo' => 'bar' ];
        self::assertTrue( $backend->reset() );
        self::assertSame( [], $backend->rSession );

        $backend->bFailReset = true;
        self::assertFalse( $backend->reset() );
    }


    public function testStart() : void {
        $backend = new MockSessionBackend( [] );
        self::assertTrue( $backend->start() );
        self::assertTrue( $backend->bActive );
        $this->expectException( LogicException::class );
        $backend->start();
    }


    public function testUnset() : void {
        $backend = new MockSessionBackend( [ 'foo' => 'bar' ] );
        $backend->start();
        self::assertTrue( $backend->unset() );
        self::assertSame( [], $backend->rSession );

        $backend->bFailUnset = true;
        self::assertFalse( $backend->unset() );

        $backend = new MockSessionBackend( [] );
        $this->expectException( LogicException::class );
        $backend->unset();
    }


}
