<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Framework\Exceptions;


use JDWX\Web\Framework\Exceptions\HttpStatusException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( HttpStatusException::class )]
final class HttpStatusExceptionTest extends TestCase {


    public function testDisplay() : void {
        $ex = new HttpStatusException( 500, 'Test Message', 'Display Message' );
        self::assertSame( 'Display Message', $ex->display() );
    }


    public function testGetCode() : void {
        $ex = new HttpStatusException( 500, 'Test Message', 'Display Message' );
        self::assertSame( 500, $ex->getCode() );
    }


}