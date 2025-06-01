<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Framework\Exceptions;


use JDWX\Web\Framework\Exceptions\MethodException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( MethodException::class )]
final class MethodExceptionTest extends TestCase {


    public function testDisplay() : void {
        $ex = new MethodException( 123, 'FOO' );
        self::assertNull( $ex->display() );

        $ex = new MethodException( 123, 'FOO', i_nstDisplay: 'BAR' );
        self::assertSame( 'BAR', $ex->display() );

        $ex = new MethodException( 123, 'FOO', i_nstDisplay: '{{ method }}BAR' );
        self::assertSame( 'FOOBAR', $ex->display() );
    }


    public function testMessage() : void {
        $ex = new MethodException( 123, 'FOO' );
        self::assertSame( '', $ex->getMessage() );

        $ex = new MethodException( 123, 'FOO', 'BAR' );
        self::assertSame( 'BAR', $ex->getMessage() );

        $ex = new MethodException( 123, 'FOO', '{{ method }}BAR' );
        self::assertSame( 'FOOBAR', $ex->getMessage() );
    }


    public function testMethod() : void {
        $ex = new MethodException( 123, 'FOO' );
        self::assertSame( 'FOO', $ex->method() );

        $ex = new MethodException( 123, 'FOO!BAR' );
        self::assertSame( 'FOO_BAR', $ex->method() );

        $ex = new MethodException( 123, 'AVeryLongMethodNameThatShouldNotBeTakenAtFaceValue' );
        self::assertSame( 'AVeryLongMeth...', $ex->method() );
    }


    public function testRawMethod() : void {
        $ex = new MethodException( 123, 'FOO' );
        self::assertSame( 'FOO', $ex->rawMethod() );

        $ex = new MethodException( 123, 'FOO!BAR' );
        self::assertSame( 'FOO!BAR', $ex->rawMethod() );

        $ex = new MethodException( 123, 'AVeryLongMethodNameThatShouldNotBeTakenAtFaceValue' );
        self::assertSame( 'AVeryLongMethodNameThatShouldNotBeTakenAtFaceValue', $ex->rawMethod() );
    }


}
