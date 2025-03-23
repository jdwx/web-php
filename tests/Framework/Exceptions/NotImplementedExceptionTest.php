<?php


declare( strict_types = 1 );


namespace Framework\Exceptions;


use JDWX\Web\Framework\Exceptions\NotImplementedException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( NotImplementedException::class )]
class NotImplementedExceptionTest extends TestCase {


    public function testGetCode() : void {
        $ex = new NotImplementedException();
        self::assertSame( 501, $ex->getCode() );
    }


}
