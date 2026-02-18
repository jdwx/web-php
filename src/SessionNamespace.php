<?php


declare( strict_types = 1 );


namespace JDWX\Web;


use JDWX\Strict\Cast;
use JDWX\Web\Backends\SessionBackendInterface;
use RuntimeException;


/**
 * Class SessionNamespace
 *
 * This class provides a namespaced session interface, allowing for the management of session variables
 * and making it easy to isolate session data within specific namespaces.
 *
 * @package JDWX\Web
 */
class SessionNamespace extends SessionBase {


    /** @var list<string> */
    private readonly array $rNamespace;


    /**
     * @param SessionBackendInterface|null $backend The session backend to use, or null for the default PHP backend.
     * @param list<string>|string $namespace The namespace path, either as a single string segment
     *                                       or a list of segments. An empty array represents the root namespace.
     * @throws RuntimeException If the specified namespace does not exist in the backend.
     */
    public function __construct( ?SessionBackendInterface $backend = null,
                                 array|string             $namespace = [] ) {
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


    /**
     * @param list<string>|string $i_namespace
     * @return self
     *
     * Returns a new SessionNamespace instance for the specified namespace
     * using the existing session backend or the default if no existing
     * backend is set.
     */
    public static function default( array|string $i_namespace = [] ) : self {
        return SessionControl::getGlobal()->namespace( $i_namespace );
    }


    /**
     * Remove all session variables in (or under) this namespace.
     * @return void
     */
    public function clear() : void {
        $this->backend->clear( $this->rNamespace );
    }


    /**
     * Retrieves a session variable by key, returning a default value if
     * the key does not exist.
     *
     * @param string $i_stKey The session variable name.
     * @param mixed $i_xDefault The value to return if the key is not set.
     * @return mixed The stored value, or $i_xDefault if the key is absent.
     * @throws \LogicException If no session is active.
     */
    public function get( string $i_stKey, mixed $i_xDefault = null ) : mixed {
        $this->checkActive();

        if ( ! $this->has( $i_stKey ) ) {
            return $i_xDefault;
        }

        return $this->backend->get( $this->rNamespace, $i_stKey );

    }


    /**
     * Retrieves a session variable as an integer. Returns the default if the
     * key is not set. Throws if neither the value nor the default is available.
     *
     * @param string $i_stKey The session variable name.
     * @param int|null $i_niDefault The fallback value if the key is not set.
     * @return int The stored integer value or the default.
     * @throws RuntimeException If the key is not set and no default was provided.
     */
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


    /**
     * Retrieves a session variable as an integer or null if the key is not set.
     *
     * @param string $i_stKey The session variable name.
     * @return int|null The stored integer value, or null if the key is absent.
     * @throws RuntimeException If the value exists but is not an integer.
     */
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


    /**
     * Retrieves a session variable as a string. Returns the default if the
     * key is not set. Throws if neither the value nor the default is available.
     *
     * @param string $i_stKey The session variable name.
     * @param string|null $i_nstDefault The fallback value if the key is not set.
     * @return string The stored string value or the default.
     * @throws RuntimeException If the key is not set and no default was provided.
     */
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


    /**
     * Retrieves a session variable as a string or null if the key is not set.
     *
     * @param string $i_stKey The session variable name.
     * @return string|null The stored string value, or null if the key is absent.
     * @throws RuntimeException If the value exists but is not a string.
     */
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


    /**
     * Checks whether a session variable exists in this namespace.
     *
     * @param string $i_stKey The session variable name.
     * @return bool True if the key exists.
     * @throws \LogicException If no session is active.
     */
    public function has( string $i_stKey ) : bool {
        $this->checkActive();
        return $this->backend->has( $this->rNamespace, $i_stKey );
    }


    /**
     * Increments a numeric session variable by the given value. If the key
     * does not exist, it is initialized to the increment value.
     *
     * @param string $i_stKey The session variable name to increment.
     * @param float|int $i_nValue The amount to increment by (default: 1).
     * @throws \LogicException If no session is active.
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


    /**
     * Returns all session variables in this namespace as a key-value array.
     *
     * @return array<string, string|list<string>> The session data in this namespace.
     * @throws \LogicException If no session is active.
     */
    public function list() : array {
        $this->checkActive();
        return $this->backend->list( $this->rNamespace );
    }


    /**
     * Creates a child namespace relative to this one. The resulting namespace
     * path is this namespace's path with the given segments appended.
     *
     * @param list<string>|string $i_namespace The child namespace path, either as a
     *        single string segment or a list of segments.
     * @return self A new SessionNamespace scoped to the child path.
     * @throws RuntimeException If the resulting namespace does not exist in the backend.
     */
    public function namespace( array|string $i_namespace ) : self {
        if ( is_string( $i_namespace ) ) {
            $i_namespace = [ $i_namespace ];
        }
        $i_namespace = array_merge( $this->rNamespace, $i_namespace );
        return new self( $this->backend, Cast::listString( $i_namespace ) );
    }


    /**
     * Removes a session variable from this namespace.
     *
     * @param string $i_stKey The session variable name to remove.
     * @throws \LogicException If no session is active.
     */
    public function remove( string $i_stKey ) : void {
        $this->checkActive();
        $this->backend->remove( $this->rNamespace, $i_stKey );
    }


    /**
     * Sets a session variable in this namespace.
     *
     * @param string $i_stKey The session variable name.
     * @param mixed $i_xValue The value to store.
     * @throws \LogicException If no session is active.
     */
    public function set( string $i_stKey, mixed $i_xValue ) : void {
        $this->checkActive();
        $this->backend->set( $this->rNamespace, $i_stKey, $i_xValue );
    }


}