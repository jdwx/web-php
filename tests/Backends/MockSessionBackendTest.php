<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Backends;


use JDWX\Strict\Exceptions\TypeException;
use JDWX\Web\Backends\AbstractSessionBackend;
use JDWX\Web\Backends\MockSessionBackend;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( AbstractSessionBackend::class )]
#[CoversClass( MockSessionBackend::class )]
class MockSessionBackendTest extends TestCase {


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
        self::expectException( TypeException::class );
        $backend->nameEx();
    }


}
