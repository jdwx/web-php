<?php


declare( strict_types = 1 );


namespace JDWX\Web;


use JDWX\Strict\Cast;
use JDWX\Web\Backends\SessionBackendInterface;
use RuntimeException;


class SessionNamespace extends SessionBase {


    /** @var list<string> */
    private readonly array $rNamespace;


    /** @param list<string>|string $namespace */
    public function __construct( SessionBackendInterface $backend,
                                 array|string            $namespace = [] ) {
        parent::__construct( $backend );
        if ( ! is_array( $namespace ) ) {
            $namespace = [ $namespace ];
        }
        if ( ! $this->backend->hasNamespace( [], $namespace ) ) {
            $stNamespace = implode( '|', $namespace );
            throw new RuntimeException( "Session namespace does not exist: {$stNamespace}" );
        }
        $this->rNamespace = $namespace;
    }


    public function get( string $i_stKey, mixed $i_xDefault = null ) : mixed {
        $this->checkActive();

        if ( ! $this->has( $i_stKey ) ) {
            return $i_xDefault;
        }

        return $this->backend->get( $this->rNamespace, $i_stKey );

    }


    public function getInt( string $i_stKey, ?int $i_niDefault = null ) : int {
        $x = $this->get( $i_stKey );
        if ( is_int( $x ) ) {
            return $x;
        }
        if ( is_int( $i_niDefault ) ) {
            return $i_niDefault;
        }
        throw new RuntimeException( "Session value {$i_stKey} is not found and no default was provided." );
    }


    public function getIntOrNull( string $i_stKey ) : ?int {
        $x = $this->get( $i_stKey );
        if ( $x === null ) {
            return null;
        }
        if ( is_int( $x ) ) {
            return $x;
        }
        throw new RuntimeException( "Session value {$i_stKey} is not an integer." );
    }


    public function getString( string $i_stKey, ?string $i_nstDefault = null ) : string {
        $x = $this->get( $i_stKey );
        if ( is_string( $x ) ) {
            return $x;
        }
        if ( is_string( $i_nstDefault ) ) {
            return $i_nstDefault;
        }
        throw new RuntimeException( "Session value {$i_stKey} is not found and no default was provided." );
    }


    public function getStringOrNull( string $i_stKey ) : ?string {
        $x = $this->get( $i_stKey );
        if ( $x === null ) {
            return null;
        }
        if ( is_string( $x ) ) {
            return $x;
        }
        throw new RuntimeException( "Session value {$i_stKey} is not a string." );
    }


    public function has( string $i_stKey ) : bool {
        $this->checkActive();
        return $this->backend->has( $this->rNamespace, $i_stKey );
    }


    /**
     * @param string $i_stKey The name of the session variable to increment.
     * @param float|int $i_nValue The value to increment by. (Default: 1)
     */
    public function increment( string $i_stKey, float|int $i_nValue = 1 ) : void {
        $this->checkActive();
        if ( ! $this->has( $i_stKey ) ) {
            $this->set( $i_stKey, $i_nValue );
        } else {
            $this->backend->set( $this->rNamespace, $i_stKey,
                $this->backend->get( $this->rNamespace, $i_stKey ) + $i_nValue );
        }
    }


    /** @return array<string, string|list<string>> */
    public function list() : array {
        $this->checkActive();
        return $this->backend->list( $this->rNamespace );
    }


    /** @param list<string>|string $i_namespace */
    public function namespace( array|string $i_namespace ) : SessionNamespace {
        if ( is_string( $i_namespace ) ) {
            $i_namespace = [ $i_namespace ];
        }
        $i_namespace = array_merge( $this->rNamespace, $i_namespace );
        return new SessionNamespace( $this->backend, Cast::listString( $i_namespace ) );
    }


    /**
     * @param string $i_stKey
     * @return void
     *
     * Remove a session variable.
     */
    public function remove( string $i_stKey ) : void {
        $this->checkActive();
        $this->backend->remove( $this->rNamespace, $i_stKey );
    }


    /**
     * @param string $i_stKey The name of the session variable to set.
     * @param mixed $i_xValue The value to set.
     * @return void
     *
     * Set a session variable.
     */
    public function set( string $i_stKey, mixed $i_xValue ) : void {
        $this->checkActive();
        $this->backend->set( $this->rNamespace, $i_stKey, $i_xValue );
    }


}