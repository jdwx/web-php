<?php


declare( strict_types = 1 );


namespace JDWX\Web\Backends;


class MockHttpBackend extends AbstractHttpBackend {


    public int $iStatus = 200;

    /** @var list<string> */
    public array $rHeaders = [];


    public function sendHeader( string $i_stHeader ) : void {
        $this->rHeaders[] = $i_stHeader;
    }


    public function setResponseCode( int $i_status ) : void {
        $this->iStatus = $i_status;
    }


}
