<?php


declare( strict_types = 1 );


namespace JDWX\Web\Backends;


use RuntimeException;


abstract class AbstractSessionBackend implements SessionBackendInterface {


    public function abortEx() : void {
        $b = $this->abort();
        if ( $b ) {
            return;
        }
        throw new RuntimeException( 'Session failed to abort.' );
    }


    public function cacheLimiterEx( ?string $value = null ) : string {
        $x = $this->cacheLimiter( $value );
        if ( is_string( $x ) ) {
            return $x;
        }
        throw new RuntimeException( 'Session failed to set cache limiter.' );
    }


    public function destroyEx() : void {
        $b = $this->destroy();
        if ( $b ) {
            return;
        }
        throw new RuntimeException( 'Session failed to destroy.' );
    }


    public function idEx( ?string $id = null ) : string {
        $x = $this->id( $id );
        if ( is_string( $x ) ) {
            return $x;
        }
        throw new RuntimeException( 'Session failed to get id.' );
    }


    public function regenerateIdEx( bool $deleteOldSession = false ) : void {
        $b = $this->regenerateId( $deleteOldSession );
        if ( $b ) {
            return;
        }
        throw new RuntimeException( 'Session failed to regenerate id.' );
    }


    public function startEx( array $options = [] ) : void {
        $b = $this->start( $options );
        if ( $b ) {
            return;
        }
        throw new RuntimeException( 'Session failed to start.' );
    }


    public function unsetEx() : void {
        $b = $this->unset();
        if ( $b ) {
            return;
        }
        throw new RuntimeException( 'Session failed to unset.' );
    }


    public function writeCloseEx() : void {
        $b = $this->writeClose();
        if ( $b ) {
            return;
        }
        throw new RuntimeException( 'Session failed to write close.' );
    }


}
