<?php /** @noinspection PhpUnused */


declare( strict_types = 1 );


namespace JDWX\Web;


use JDWX\Web\Backends\PHPSessionBackend;
use JDWX\Web\Backends\SessionBackendInterface;
use LogicException;
use Psr\Log\LoggerInterface;


/**
 * Class SessionHelper
 *
 * This class encapsulates the PHP session handler functions, performing error
 * and type checking, which may make it easier to use a different methodology
 * in the future.
 *
 * Methods are grouped into several categories. The first group is used for
 * manipulating top-level values. The second group is used for manipulating
 * nested values, which is mainly useful for namespacing session variables
 * when you don't know what else you might be sharing the session with.
 * (Using nested variables is highly encouraged.) The third group is used
 * for manipulating the session as a whole. The fourth group is used for
 * testing and debugging.
 *
 * Methods used for manipulating top-level values:
 *
 * - get()
 * - getInt()
 * - getIntOrNull()
 * - getString()
 * - getStringOrNull()
 * - has()
 * - increment()
 * - remove()
 *
 * Methods used for manipulating nested values:
 *
 * - nestedGet()
 * - nestedGetInt()
 * - nestedGetIntOrNull()
 * - nestedGetString()
 * - nestedGetStringOrNull()
 * - nestedHas()
 * - nestedIncrement()
 * - nestedRemove()
 *
 * Methods used for manipulating the session as a whole:
 *
 * - abort()
 * - active()
 * - cacheLimiter()
 * - destroy()
 * - flush()
 * - id()
 * - list()
 * - peek()
 * - regenerate()
 * - reset()
 * - softStart()
 * - start()
 * - unset()
 * - writeClose()
 *
 * Methods used internally or for testing and debugging:
 * - backend()
 * - checkActive()
 * - init()
 *
 */
class Session {


    protected static ?SessionInterface $backend = null;


    /**
     * @return void
     *
     * Abort the session without saving any changes to the session data.
     */
    public static function abort() : void {
        static::backend()->abort();
    }


    /**
     * @return bool True if a session is active, false otherwise.
     */
    public static function active() : bool {
        return static::backend()->active();
    }


    public static function cacheLimiter( ?string $i_nstCacheLimiter = null ) : string {
        return static::backend()->cacheLimiter( $i_nstCacheLimiter );
    }


    public static function cookieInRequest( ?RequestInterface $i_req = null ) : bool {
        return static::backend()->cookieInRequest( $i_req );
    }


    public static function destroy() : void {
        static::backend()->destroy();
    }


    public static function flush() : void {
        static::backend()->flush();
    }


    public static function get( string $i_stKey, mixed $i_xDefault = null ) : mixed {
        return static::backend()->get( $i_stKey, $i_xDefault );
    }


    public static function getInt( string $i_stKey, ?int $i_niDefault = null ) : int {
        return static::backend()->getInt( $i_stKey, $i_niDefault );
    }


    public static function getIntOrNull( string $i_stKey ) : ?int {
        return static::backend()->getIntOrNull( $i_stKey );
    }


    public static function getString( string $i_stKey, ?string $i_nstDefault = null ) : string {
        return static::backend()->getString( $i_stKey, $i_nstDefault );
    }


    public static function getStringOrNull( string $i_stKey ) : ?string {
        return static::backend()->getStringOrNull( $i_stKey );
    }


    public static function has( string $i_stKey ) : bool {
        return static::backend()->has( $i_stKey );
    }


    public static function id() : string {
        return static::backend()->id();
    }


    /**
     * @param string $i_stKey The name of the session variable to increment.
     * @param float|int $i_nValue The value to increment by. (Default: 1)
     */
    public static function increment( string $i_stKey, float|int $i_nValue = 1 ) : void {
        static::backend()->increment( $i_stKey, $i_nValue );
    }


    /**
     * @param SessionBackendInterface $i_backend The session backend to use.
     * @return void
     *
     * Initialize the session handler. Only used for testing.
     */
    public static function init( SessionBackendInterface $i_backend ) : void {
        static::$backend = new MainSession( $i_backend );
    }


    /** @return array<string, string|list<string>> */
    public static function list() : array {
        return static::backend()->list();
    }


    /**
     * @param string $i_stKey1
     * @param string $i_stKey2
     * @return mixed The found value or null if not found.
     *
     * Get a session variable in a namespaced hierarchy. Note that it is not
     * possible to distinguish between a null value and a non-existent value.
     */
    public static function nestedGet( string $i_stKey1, string $i_stKey2 ) : mixed {
        return static::backend()->nestedGet( $i_stKey1, $i_stKey2 );
    }


    /**
     * @param string $i_stKey1
     * @param string $i_stKey2
     * @param int|null $i_niDefault
     * @return int
     *
     * Get a session variable in a namespaced hierarchy, requiring it to exist
     * and be an integer (with an optional default value if it does not exist).
     */
    public static function nestedGetInt( string $i_stKey1, string $i_stKey2, ?int $i_niDefault = null ) : int {
        return static::backend()->nestedGetInt( $i_stKey1, $i_stKey2, $i_niDefault );
    }


    /**
     * @param string $i_stKey1
     * @param string $i_stKey2
     * @return int|null
     *
     * Get a session variable in a namespaced hierarchy, requiring it to be
     * an integer if it exists. (Note it is not possible to distinguish
     * between a null value and a non-existent value.)
     */
    public static function nestedGetIntOrNull( string $i_stKey1, string $i_stKey2 ) : ?int {
        return static::backend()->nestedGetIntOrNull( $i_stKey1, $i_stKey2 );
    }


    /**
     * @param string $i_stKey1
     * @param string $i_stKey2
     * @param string|null $i_nstDefault
     * @return string
     *
     * Get a session variable in a namespaced hierarchy, requiring it to exist
     * and be a string (with an optional default value if it doesn't).
     */
    public static function nestedGetString( string  $i_stKey1, string $i_stKey2,
                                            ?string $i_nstDefault = null ) : string {
        return static::backend()->nestedGetString( $i_stKey1, $i_stKey2, $i_nstDefault );
    }


    /**
     * @param string $i_stKey1
     * @param string $i_stKey2
     * @return string|null
     *
     * Get a session variable in a namespaced hierarchy, requiring it to be
     * a string if it exists. (Note it is not possible to distinguish
     * between a null value and a non-existent value.)
     */
    public static function nestedGetStringOrNull( string $i_stKey1, string $i_stKey2 ) : ?string {
        return static::backend()->nestedGetStringOrNull( $i_stKey1, $i_stKey2 );
    }


    public static function nestedHas( string $i_stKey1, string $i_stKey2 ) : bool {
        return static::backend()->nestedHas( $i_stKey1, $i_stKey2 );
    }


    /**
     * @param string $i_stKey1
     * @param string $i_stKey2
     * @param float|int $i_nValue
     * @return void
     *
     * Increment a session variable in a namespaced hierarchy.
     */
    public static function nestedIncrement( string $i_stKey1, string $i_stKey2, float|int $i_nValue = 1 ) : void {
        static::backend()->nestedIncrement( $i_stKey1, $i_stKey2, $i_nValue );
    }


    /**
     * @param string $i_stKey1
     * @param string $i_stKey2
     * @return void
     *
     * Remove a session variable in a namespaced hierarchy.
     */
    public static function nestedRemove( string $i_stKey1, string $i_stKey2 ) : void {
        static::backend()->nestedRemove( $i_stKey1, $i_stKey2 );
    }


    /**
     * @param string $i_stKey1 The first key.
     * @param string $i_stKey2 The second key.
     * @param mixed $i_xValue The value to set.
     * @return void
     *
     * Simplifies setting a session variable in a two-level hierarchy.
     */
    public static function nestedSet( string $i_stKey1, string $i_stKey2, mixed $i_xValue ) : void {
        static::backend()->nestedSet( $i_stKey1, $i_stKey2, $i_xValue );
    }


    /**
     * @return array<string, string|list<string>>
     *
     * Return the session data while the session is not active.
     */
    public static function peek() : array {
        return static::backend()->peek();
    }


    /**
     * @param bool $i_bDeleteOld
     * @return void
     *
     * Regenerate the session ID while preserving the session data.
     */
    public static function regenerate( bool $i_bDeleteOld = false ) : void {
        static::backend()->regenerate( $i_bDeleteOld );
    }


    /**
     * @param string $i_stKey
     * @return void
     *
     * Remove a session variable.
     */
    public static function remove( string $i_stKey ) : void {
        static::backend()->remove( $i_stKey );
    }


    /**
     * @return void
     *
     * Reset the session data to its state when the session was started.
     *
     */
    public static function reset( bool $i_bPreserveTimes = true ) : void {
        static::backend()->reset( $i_bPreserveTimes );
    }


    /**
     * @param string $i_stKey The name of the session variable to set.
     * @param mixed $i_xValue The value to set.
     * @return void
     *
     * Set a session variable.
     */
    public static function set( string $i_stKey, mixed $i_xValue ) : void {
        static::backend()->set( $i_stKey, $i_xValue );
    }


    /**
     * Start a session if one is not already active.
     */
    public static function softStart( ?LoggerInterface  $i_logger = null, ?string $i_stSessionName = null,
                                      ?RequestInterface $i_req = null ) : bool {
        return static::backend()->softStart( $i_logger, $i_stSessionName, $i_req );
    }


    public static function start( ?LoggerInterface  $i_logger = null, ?string $i_stSessionName = null,
                                  ?RequestInterface $i_req = null ) : bool {
        return static::backend()->start( $i_logger, $i_stSessionName, $i_req );
    }


    /**
     * @return void
     *
     * Removes all session data. The session remains active.
     */
    public static function unset() : void {
        static::backend()->unset();
    }


    /**
     * @return void
     *
     * Write the session data and close the session. Used if you
     * need to do additional processing after writing the session data
     * but don't want to block potential other requests that might
     * need to access the session.
     */
    public static function writeClose() : void {
        static::backend()->writeClose();
    }


    protected static function backend() : SessionInterface {
        if ( ! static::$backend instanceof SessionInterface ) {
            static::init( new PHPSessionBackend() );
        }
        return static::$backend ?? throw new LogicException( 'Session backend not initialized.' );
    }


}
