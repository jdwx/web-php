<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Framework\Exceptions;


use JDWX\Web\Framework\Exceptions\MethodNotAllowedException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( MethodNotAllowedException::class )]
final class MethodNotAllowedExceptionTest extends TestCase {


    public function testGetCode() : void {
        $ex = new MethodNotAllowedException( 'Test Message' );
        self::assertSame( 405, $ex->getCode() );
    }


}
