<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Framework\Exceptions;


use JDWX\Web\Framework\Exceptions\ForbiddenException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( ForbiddenException::class )]
final class ForbiddenExceptionTest extends TestCase {


    public function testGetCode() : void {
        $e = new ForbiddenException();
        self::assertEquals( 403, $e->getCode() );
    }


}