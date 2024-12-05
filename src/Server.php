<?php /** @noinspection PhpUnused */


declare( strict_types = 1 );


namespace JDWX\Web;


/** This class encapsulates the $_SERVER superglobal to allow type & error checking. */
class Server {


    public static function documentRoot() : string {
        return $_SERVER[ 'DOCUMENT_ROOT' ];
    }


    public static function httpHost() : string {
        return $_SERVER[ 'HTTP_HOST' ];
    }


    public static function httpReferer() : string {
        return $_SERVER[ 'HTTP_REFERER' ];
    }


    public static function httpUserAgent() : string {
        return $_SERVER[ 'HTTP_USER_AGENT' ];
    }


    public static function https() : bool {
        return $_SERVER[ 'HTTPS' ] == 'on';
    }


    public static function pathInfo() : string {
        return $_SERVER[ 'PATH_INFO' ];
    }


    public static function phpSelf() : string {
        return $_SERVER[ 'PHP_SELF' ];
    }


    public static function remotePort() : int {
        return (int) $_SERVER[ 'REMOTE_PORT' ];
    }


    public static function requestMethod() : string {
        return $_SERVER[ 'REQUEST_METHOD' ];
    }


    public static function requestScheme() : string {
        return $_SERVER[ 'REQUEST_SCHEME' ];
    }


    public static function requestUri() : string {
        return $_SERVER[ 'REQUEST_URI' ];
    }


    public static function scriptFilename() : string {
        return $_SERVER[ 'SCRIPT_FILENAME' ];
    }


    public static function scriptName() : string {
        return $_SERVER[ 'SCRIPT_NAME' ];
    }


    public static function serverAddr() : string {
        return $_SERVER[ 'SERVER_ADDR' ];
    }


    public static function serverName() : string {
        return $_SERVER[ 'SERVER_NAME' ];
    }


}
