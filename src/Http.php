<?php


declare( strict_types = 1 );


namespace JDWX\Web;


use JDWX\Web\Backends\HttpBackendInterface;


class Http {


    private static ?HttpBackendInterface $backend = null;


    public static function clear() : void {
        self::$backend = null;
    }


    public static function getResponseCode() : int {
        return self::backend()->getResponseCode();
    }


    public static function headersSent() : bool {
        return self::backend()->headersSent();
    }


    public static function init( HttpBackendInterface $i_backend ) : void {
        self::$backend = $i_backend;
    }


    public static function setHeader( string $i_stHeader ) : void {
        self::backend()->setHeader( $i_stHeader );
    }


    public static function setResponseCode( int $i_status ) : void {
        self::backend()->setResponseCode( $i_status );
    }


    protected static function backend() : HttpBackendInterface {
        if ( ! self::$backend ) {
            self::init( new Backends\PHPHttpBackend() );
        }
        return self::$backend;
    }


}
