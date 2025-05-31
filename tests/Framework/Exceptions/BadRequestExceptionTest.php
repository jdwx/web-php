<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Framework\Exceptions;


use JDWX\Web\Framework\Exceptions\BadRequestException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( BadRequestException::class )]
final class BadRequestExceptionTest extends TestCase {


    public function testGetCode() : void {
        $ex = new BadRequestException( 'Test Message' );
        self::assertSame( 400, $ex->getCode() );
    }


}
