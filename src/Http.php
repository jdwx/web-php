<?php


declare( strict_types = 1 );


namespace JDWX\Web;


use JDWX\Web\Backends\HttpBackendInterface;


class Http {


    private static ?HttpBackendInterface $backend = null;


    public static function init( HttpBackendInterface $i_backend ) : void {
        self::$backend = $i_backend;
    }


    public static function sendHeader( string $i_stHeader ) : void {
        self::backend()->sendHeader( $i_stHeader );
    }


    public static function setResponseCode( int $i_status ) : void {
        self::backend()->setResponseCode( $i_status );
    }


    private static function backend() : HttpBackendInterface {
        if ( ! self::$backend ) {
            self::init( new Backends\PHPHttpBackend() );
        }
        return self::$backend;
    }


}
