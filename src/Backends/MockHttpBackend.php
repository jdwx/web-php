<?php


declare( strict_types = 1 );


namespace JDWX\Web\Backends;


class MockHttpBackend extends AbstractHttpBackend {


    public static bool $bHeadersSent = false;

    /** @var list<string> */
    public array $rHeaders = [];

    private int $iStatus = 200;


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
