<?php


declare( strict_types = 1 );


namespace Shims;


trait HttpTesterTrait {


    public int $iStatus = 200;

    /** @var list<string> */
    public array $rHeaders = [];


    protected function sendHeader( string $i_stHeader ) : void {
        $this->rHeaders[] = $i_stHeader;
    }


    protected function setResponseCode( int $i_status ) : void {
        $this->iStatus = $i_status;
    }


}