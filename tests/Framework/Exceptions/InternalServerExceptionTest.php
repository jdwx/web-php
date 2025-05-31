<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Framework\Exceptions;


use JDWX\Web\Framework\Exceptions\InternalServerException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( InternalServerException::class )]
final class InternalServerExceptionTest extends TestCase {


    public function testGetCode() : void {
        $ex = new InternalServerException( 'Test Message' );
        self::assertSame( 500, $ex->getCode() );
    }


}
