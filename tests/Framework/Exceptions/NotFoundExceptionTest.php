<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Framework\Exceptions;


use JDWX\Web\Framework\Exceptions\NotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( NotFoundException::class )]
final class NotFoundExceptionTest extends TestCase {


    public function testGetCode() : void {
        $ex = new NotFoundException( 'Test Message' );
        self::assertSame( 404, $ex->getCode() );
    }


}
