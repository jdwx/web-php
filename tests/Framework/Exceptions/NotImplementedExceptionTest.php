<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Framework\Exceptions;


use JDWX\Web\Framework\Exceptions\NotImplementedException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( NotImplementedException::class )]
class NotImplementedExceptionTest extends TestCase {


    public function testGetCode() : void {
        $ex = new NotImplementedException( 'FOO' );
        self::assertSame( 501, $ex->getCode() );
    }


}
