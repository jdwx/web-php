<?php


declare( strict_types = 1 );


namespace JDWX\Web\Backends;


class PHPHttpBackend extends AbstractHttpBackend {


    public function sendHeader( string $i_stHeader ) : void {
        header( $i_stHeader );
    }


    public function setResponseCode( int $i_status ) : void {
        http_response_code( $i_status );
    }


}
