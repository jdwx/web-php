<?php


declare( strict_types = 1 );


namespace JDWX\Web\Backends;


class MockHttpBackend extends AbstractHttpBackend {


    public static bool $bHeadersSent = false;

    /** @var list<string> */
    public array $rHeaders = [];

    private int $iStatus = 200;


    /** @return string|list<string> */
    public function getHeader( string $i_stHeader ) : string|array {
        $r = [];
        $i_stHeader = strtolower( $i_stHeader ) . ':';
        foreach ( $this->rHeaders as $stHeader ) {
            if ( str_starts_with( strtolower( $stHeader ), $i_stHeader ) ) {
                $r[] = trim( substr( $stHeader, strlen( $i_stHeader ) ) );
            }
        }
        if ( 1 === count( $r ) ) {
            return $r[0];
        }
        return $r;
    }


    public function getResponseCode() : int {
        return $this->iStatus;
    }


    /**
     * Note that you have to poke the variable manually.
     * Regrettably, there is no way to check if headers have been sent
     * under PhpUnit. (header_register_callback() doesn't work.)
     *
     * @return bool True if headers have been sent.
     */
    public function headersSent() : bool {
        return self::$bHeadersSent;
    }


    public function setHeader( string $i_stHeader ) : void {
        $this->rHeaders[] = $i_stHeader;
    }


    public function setResponseCode( int $i_status ) : void {
        $this->iStatus = $i_status;
    }


}
