<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests;


use JDWX\Web\Flush;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( Flush::class )]
final class FlushTest extends TestCase {


    public function testToString() : void {
        $flush = new Flush();
        self::assertSame( '', (string) $flush );
    }


}
