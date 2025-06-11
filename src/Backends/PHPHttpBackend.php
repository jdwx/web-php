<?php


declare( strict_types = 1 );


namespace JDWX\Web\Backends;


use JDWX\Strict\OK;


/**
 *
 * This is the HTTP stuff that PHP provides that cannot be tested
 * from unit tests.
 *
 * @codeCoverageIgnore
 */
class PHPHttpBackend extends AbstractHttpBackend {


    public function getResponseCode() : int {
        return OK::http_response_code();
    }


    public function headersSent() : bool {
        return headers_sent();
    }


    public function setHeader( string $i_stHeader ) : void {
        header( $i_stHeader );
    }


    public function setResponseCode( int $i_status ) : void {
        http_response_code( $i_status );
    }


}
