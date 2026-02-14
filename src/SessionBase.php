<?php


declare( strict_types = 1 );


namespace JDWX\Web;


use JDWX\Web\Backends\PHPSessionBackend;
use JDWX\Web\Backends\SessionBackendInterface;
use LogicException;


class SessionBase {


    protected readonly SessionBackendInterface $backend;


    public function __construct( ?SessionBackendInterface $backend = null ) {
        $this->backend = $backend ?? new PHPSessionBackend();
    }


    /**
     * @return bool True if a session is active, false otherwise.
     */
    public function active() : bool {
        return $this->backend->status() === PHP_SESSION_ACTIVE;
    }


    /** Returns the underlying session backend implementation. */
    public function backend() : SessionBackendInterface {
        return $this->backend;
    }


    /**
     * @return void
     *
     * Check if a session is active and throw an exception if it is not.
     */
    protected function checkActive() : void {
        if ( ! $this->active() ) {
            throw new LogicException( 'Session not started.' );
        }
    }


}
