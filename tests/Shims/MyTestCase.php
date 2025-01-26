<?php


declare( strict_types = 1 );


namespace Shims;


use JDWX\Web\Backends\MockHttpBackend;
use JDWX\Web\Http;
use PHPUnit\Framework\TestCase;


class MyTestCase extends TestCase {


    protected MockHttpBackend $http;


    protected function setUp() : void {
        $this->http = new MockHttpBackend();
        Http::init( $this->http );
    }


}
