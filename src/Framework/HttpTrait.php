<?php


declare( strict_types = 1 );


namespace JDWX\Web\Framework;


use JDWX\Web\Http;


trait HttpTrait {


    protected function setHeader( string $i_stHeader ) : void {
        Http::setHeader( $i_stHeader );
    }


    protected function setResponseCode( int $i_status ) : void {
        Http::setResponseCode( $i_status );
    }


}