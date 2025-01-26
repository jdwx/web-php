<?php


declare( strict_types = 1 );


namespace JDWX\Web\Framework;


use JDWX\Web\Http;


trait HttpTrait {


    protected function sendHeader( string $i_stHeader ) : void {
        Http::sendHeader( $i_stHeader );
    }


    protected function setResponseCode( int $i_status ) : void {
        Http::setResponseCode( $i_status );
    }


}