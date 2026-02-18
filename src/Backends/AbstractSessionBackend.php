<?php


declare( strict_types = 1 );


namespace JDWX\Web\Backends;


use JDWX\Strict\TypeIs;
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


    public function clear( array|string $i_namespace = [] ) : void {
        if ( ! is_array( $i_namespace ) ) {
            $i_namespace = [ $i_namespace ];
        }
        $stTag = array_pop( $i_namespace );
        $rSession =& $this->getNamespace( $i_namespace );
        if ( $stTag ) {
            # We are clearing a sub-namespace.
            $rSession[ $stTag ] = [];
        } else {
            # We are clearing the entire global namespace.
            foreach ( array_keys( $rSession ) as $stKey ) {
                unset( $rSession[ $stKey ] );
            }
        }
    }


    public function destroyEx() : void {
        $b = $this->destroy();
        if ( $b ) {
            return;
        }
        throw new RuntimeException( 'Session failed to destroy.' );
    }


    public function hasNamespace( array $namespace, array $subNamespace ) : bool {
        $r = $this->getNamespace( $namespace );
        foreach ( $subNamespace as $stKey ) {
            if ( ! isset( $r[ $stKey ] ) ) {
                return true;
            }
            if ( ! is_array( $r[ $stKey ] ) ) {
                return false;
            }
            $r = $r[ $stKey ];
        }
        return true;
    }


    public function idEx( ?string $id = null ) : string {
        $x = $this->id( $id );
        if ( is_string( $x ) ) {
            return $x;
        }
        throw new RuntimeException( 'Session failed to get id.' );
    }


    public function nameEx( ?string $value = null ) : string {
        return TypeIs::string( $this->name( $value ) );
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


    /**
     * @param list<string> $namespace
     * @return array<string, mixed>
     */
    abstract protected function & getNamespace( array $namespace ) : array;


}
