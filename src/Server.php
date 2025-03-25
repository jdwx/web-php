<?php /** @noinspection PhpUnused */


declare( strict_types = 1 );


namespace JDWX\Web;


/** This class encapsulates the $_SERVER superglobal to allow type & error checking. */
class Server implements ServerInterface {


    public function documentRoot() : string {
        return $_SERVER[ 'DOCUMENT_ROOT' ];
    }


    public function httpHost() : string {
        return $_SERVER[ 'HTTP_HOST' ];
    }


    public function httpReferer() : string {
        return $_SERVER[ 'HTTP_REFERER' ];
    }


    public function httpUserAgent() : string {
        return $_SERVER[ 'HTTP_USER_AGENT' ];
    }


    public function https() : bool {
        return $_SERVER[ 'HTTPS' ] == 'on';
    }


    public function pathInfo() : string {
        return $_SERVER[ 'PATH_INFO' ];
    }


    public function phpSelf() : string {
        return $_SERVER[ 'PHP_SELF' ];
    }


    public function remoteAddr() : string {
        return $_SERVER[ 'REMOTE_ADDR' ];
    }


    public function remotePort() : int {
        return (int) $_SERVER[ 'REMOTE_PORT' ];
    }


    public function requestMethod() : string {
        return $_SERVER[ 'REQUEST_METHOD' ];
    }


    public function requestScheme() : string {
        return $_SERVER[ 'REQUEST_SCHEME' ];
    }


    public function requestUri() : string {
        return $_SERVER[ 'REQUEST_URI' ];
    }


    public function scriptFilename() : string {
        return $_SERVER[ 'SCRIPT_FILENAME' ];
    }


    public function scriptName() : string {
        return $_SERVER[ 'SCRIPT_NAME' ];
    }


    public function serverAddr() : string {
        return $_SERVER[ 'SERVER_ADDR' ];
    }


    public function serverName() : string {
        return $_SERVER[ 'SERVER_NAME' ];
    }


}
