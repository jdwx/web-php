<?php


declare( strict_types = 1 );


namespace JDWX\Web\Backends;


interface HttpBackendInterface {


    public function sendHeader( string $i_stHeader ) : void;


    public function setResponseCode( int $i_status ) : void;


}