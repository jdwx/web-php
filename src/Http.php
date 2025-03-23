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


    public static function setHeader( string $i_stHeader, ?string $i_nstValue = null ) : void {
        if ( is_string( $i_nstValue ) ) {
            $i_stHeader .= ": {$i_nstValue}";
        }
        self::backend()->setHeader( $i_stHeader );
    }


    /** @param iterable<string> $i_rHeaders */
    public static function setHeaders( iterable $i_rHeaders ) : void {
        foreach ( $i_rHeaders as $header ) {
            self::setHeader( $header );
        }
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
