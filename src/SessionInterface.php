<?php


declare( strict_types = 1 );


namespace JDWX\Web;


use Psr\Log\LoggerInterface;


interface SessionInterface {


    public function abort() : void;


    /**
     * @return bool True if a session is active, false otherwise.
     */
    public function active() : bool;


    public function cacheLimiter( ?string $i_nstCacheLimiter = null ) : string;


    public function cookieInRequest( ?RequestInterface $i_req = null ) : bool;


    public function destroy() : void;


    public function flush() : void;


    public function get( string $i_stKey, mixed $i_xDefault = null ) : mixed;


    public function getInt( string $i_stKey, ?int $i_niDefault = null ) : int;


    public function getIntOrNull( string $i_stKey ) : ?int;


    public function getString( string $i_stKey, ?string $i_nstDefault = null ) : string;


    public function getStringOrNull( string $i_stKey ) : ?string;


    public function has( string $i_stKey ) : bool;


    public function id() : string;


    /**
     * @param string $i_stKey The name of the session variable to increment.
     * @param float|int $i_nValue The value to increment by. (Default: 1)
     */
    public function increment( string $i_stKey, float|int $i_nValue = 1 ) : void;


    /** @return array<string, string|list<string>> */
    public function list() : array;


    /**
     * @param string $i_stKey1
     * @param string $i_stKey2
     * @return mixed The found value or null if not found.
     *
     * Get a session variable in a namespaced hierarchy. Note that it is not
     * possible to distinguish between a null value and a non-existent value.
     */
    public function nestedGet( string $i_stKey1, string $i_stKey2 ) : mixed;


    /**
     * @param string $i_stKey1
     * @param string $i_stKey2
     * @param int|null $i_niDefault
     * @return int
     *
     * Get a session variable in a namespaced hierarchy, requiring it to exist
     * and be an integer (with an optional default value if it does not exist).
     */
    public function nestedGetInt( string $i_stKey1, string $i_stKey2, ?int $i_niDefault = null ) : int;


    /**
     * @param string $i_stKey1
     * @param string $i_stKey2
     * @return int|null
     *
     * Get a session variable in a namespaced hierarchy, requiring it to be
     * an integer if it exists. (Note it is not possible to distinguish
     * between a null value and a non-existent value.)
     */
    public function nestedGetIntOrNull( string $i_stKey1, string $i_stKey2 ) : ?int;


    /**
     * @param string $i_stKey1
     * @param string $i_stKey2
     * @param string|null $i_nstDefault
     * @return string
     *
     * Get a session variable in a namespaced hierarchy, requiring it to exist
     * and be a string (with an optional default value if it doesn't).
     */
    public function nestedGetString( string $i_stKey1, string $i_stKey2, ?string $i_nstDefault = null ) : string;


    /**
     * @param string $i_stKey1
     * @param string $i_stKey2
     * @return string|null
     *
     * Get a session variable in a namespaced hierarchy, requiring it to be
     * a string if it exists. (Note it is not possible to distinguish
     * between a null value and a non-existent value.)
     */
    public function nestedGetStringOrNull( string $i_stKey1, string $i_stKey2 ) : ?string;


    /**
     * @param string $i_stKey1
     * @param string $i_stKey2
     * @return bool
     *
     * Check if a session variable in a namespaced hierarchy exists.
     */
    public function nestedHas( string $i_stKey1, string $i_stKey2 ) : bool;


    /**
     * @param string $i_stKey1
     * @param string $i_stKey2
     * @param float|int $i_nValue
     * @return void
     *
     * Increment a session variable in a namespaced hierarchy.
     */
    public function nestedIncrement( string $i_stKey1, string $i_stKey2, float|int $i_nValue = 1 ) : void;


    /**
     * @param string $i_stKey1
     * @param string $i_stKey2
     * @return void
     *
     * Remove a session variable in a namespaced hierarchy.
     */
    public function nestedRemove( string $i_stKey1, string $i_stKey2 ) : void;


    /**
     * @param string $i_stKey1 The first key.
     * @param string $i_stKey2 The second key.
     * @param mixed $i_xValue The value to set.
     * @return void
     *
     * Simplifies setting a session variable in a two-level hierarchy.
     */
    public function nestedSet( string $i_stKey1, string $i_stKey2, mixed $i_xValue ) : void;


    /**
     * @return array<string, string|list<string>>
     *
     * Return the session data while the session is not active.
     */
    public function peek() : array;


    /**
     * @param bool $i_bDeleteOld
     * @return void
     *
     * Regenerate the session ID while preserving the session data.
     */
    public function regenerate( bool $i_bDeleteOld = false ) : void;


    /**
     * @param string $i_stKey
     * @return void
     *
     * Remove a session variable.
     */
    public function remove( string $i_stKey ) : void;


    /**
     * @return void
     *
     * Reset the session data to its state when the session was started.
     *
     */
    public function reset( bool $i_bPreserveTimes = true ) : void;


    /**
     * @param string $i_stKey The name of the session variable to set.
     * @param mixed $i_xValue The value to set.
     * @return void
     *
     * Set a session variable.
     */
    public function set( string $i_stKey, mixed $i_xValue ) : void;


    /**
     * Start a session if one is not already active.
     */
    public function softStart( ?LoggerInterface  $i_logger = null, ?string $i_stSessionName = null,
                               ?RequestInterface $i_req = null ) : bool;


    /**
     * @param LoggerInterface|null $i_logger
     * @param string|null $i_stSessionName
     * @param RequestInterface|null $i_req
     * @return bool
     *
     * Start a session. If a session is already active, an exception is thrown.
     */
    public function start( ?LoggerInterface  $i_logger = null, ?string $i_stSessionName = null,
                           ?RequestInterface $i_req = null ) : bool;


    /**
     * @return void
     *
     * Removes all session data. The session remains active.
     */
    public function unset() : void;


    /**
     * @return void
     *
     * Write the session data and close the session. Used if you
     * need to do additional processing after writing the session data
     * but don't want to block potential other requests that might
     * need to access the session.
     */
    public function writeClose() : void;


}