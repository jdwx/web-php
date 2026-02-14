<?php /** @noinspection PhpDeprecationInspection */


/** @noinspection PhpUnused */


declare( strict_types = 1 );


namespace JDWX\Web;


use JDWX\Web\Backends\MockSessionBackend;
use JDWX\Web\Backends\PHPSessionBackend;
use JDWX\Web\Backends\SessionBackendInterface;
use Psr\Log\LoggerInterface;


/**
 * Class Session
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
 * @deprecated Use SessionControl and SessionNamespace instead. Deprecated for 3.x, will remove for 4.x.
 */
class Session {


    private static ?SessionBackendInterface $backend = null;

    private static ?SessionControl $control = null;


    /**
     * @return void
     *
     * Abort the session without saving any changes to the session data.
     */
    public static function abort() : void {
        static::control()->abort();
    }


    /**
     * @return bool True if a session is active, false otherwise.
     */
    public static function active() : bool {
        return static::control()->active();
    }


    public static function cacheLimiter( ?string $i_nstCacheLimiter = null ) : string {
        return static::control()->cacheLimiter( $i_nstCacheLimiter );
    }


    public static function control() : SessionControl {
        if ( ! self::$control instanceof SessionControl ) {
            self::$control = new SessionControl( static::backend() );
        }
        return self::$control ?? throw new \LogicException( 'Session control not initialized.' );
    }


    public static function cookieInRequest( ?RequestInterface $i_req = null ) : bool {
        return static::control()->cookieInRequest( $i_req );
    }


    public static function destroy() : void {
        static::control()->destroy();
    }


    public static function flush() : void {
        static::control()->flush();
    }


    public static function get( string $i_stKey, mixed $i_xDefault = null ) : mixed {
        return static::vars()->get( $i_stKey, $i_xDefault );
    }


    public static function getInt( string $i_stKey, ?int $i_niDefault = null ) : int {
        return static::vars()->getInt( $i_stKey, $i_niDefault );
    }


    public static function getIntOrNull( string $i_stKey ) : ?int {
        return static::vars()->getIntOrNull( $i_stKey );
    }


    public static function getString( string $i_stKey, ?string $i_nstDefault = null ) : string {
        return static::vars()->getString( $i_stKey, $i_nstDefault );
    }


    public static function getStringOrNull( string $i_stKey ) : ?string {
        return static::vars()->getStringOrNull( $i_stKey );
    }


    public static function has( string $i_stKey ) : bool {
        return static::vars()->has( $i_stKey );
    }


    public static function id() : string {
        return static::control()->id();
    }


    /**
     * @param string $i_stKey The name of the session variable to increment.
     * @param float|int $i_nValue The value to increment by. (Default: 1)
     */
    public static function increment( string $i_stKey, float|int $i_nValue = 1 ) : void {
        static::vars()->increment( $i_stKey, $i_nValue );
    }


    /**
     * @param SessionBackendInterface|null $i_backend The session backend to use.
     * @return void
     *
     * Initialize the session handler. Only used for testing.
     */
    public static function init( SessionBackendInterface|null $i_backend = null ) : void {
        if ( ! $i_backend instanceof SessionBackendInterface ) {
            $i_backend = new PHPSessionBackend();
        }
        self::$backend = $i_backend;
        self::$control = null;
    }


    /** @return array<string, string|list<string>> */
    public static function list() : array {
        return static::vars()->list();
    }


    /** @param array<string, mixed> $rBackup */
    public static function mock( array $rBackup = [] ) : MockSessionBackend {
        $be = new MockSessionBackend( $rBackup );
        self::init( $be );
        return $be;
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
        return static::vars( $i_stKey1 )->get( $i_stKey2 );
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
        return static::vars( $i_stKey1 )->getInt( $i_stKey2, $i_niDefault );
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
        return static::vars( $i_stKey1 )->getIntOrNull( $i_stKey2 );
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
        return static::vars( $i_stKey1 )->getString( $i_stKey2, $i_nstDefault );
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
        return static::vars( $i_stKey1 )->getStringOrNull( $i_stKey2 );
    }


    public static function nestedHas( string $i_stKey1, string $i_stKey2 ) : bool {
        return static::vars( $i_stKey1 )->has( $i_stKey2 );
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
        static::vars( $i_stKey1 )->increment( $i_stKey2, $i_nValue );
    }


    /**
     * @param string $i_stKey1
     * @param string $i_stKey2
     * @return void
     *
     * Remove a session variable in a namespaced hierarchy.
     */
    public static function nestedRemove( string $i_stKey1, string $i_stKey2 ) : void {
        static::vars( $i_stKey1 )->remove( $i_stKey2 );
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
        static::vars( $i_stKey1 )->set( $i_stKey2, $i_xValue );
    }


    /**
     * @return array<string, string|list<string>>
     *
     * Return the session data while the session is not active.
     */
    public static function peek() : array {
        return static::control()->peek();
    }


    /**
     * @param bool $i_bDeleteOld
     * @return void
     *
     * Regenerate the session ID while preserving the session data.
     */
    public static function regenerate( bool $i_bDeleteOld = false ) : void {
        static::control()->regenerate( $i_bDeleteOld );
    }


    /**
     * @param string $i_stKey
     * @return void
     *
     * Remove a session variable.
     */
    public static function remove( string $i_stKey ) : void {
        static::vars()->remove( $i_stKey );
    }


    /**
     * @return void
     *
     * Reset the session data to its state when the session was started.
     *
     */
    public static function reset( bool $i_bPreserveTimes = true ) : void {
        static::control()->reset( $i_bPreserveTimes );
    }


    /**
     * @param string $i_stKey The name of the session variable to set.
     * @param mixed $i_xValue The value to set.
     * @return void
     *
     * Set a session variable.
     */
    public static function set( string $i_stKey, mixed $i_xValue ) : void {
        static::vars()->set( $i_stKey, $i_xValue );
    }


    /**
     * Start a session if one is not already active.
     */
    public static function softStart( ?LoggerInterface  $i_logger = null, ?string $i_stSessionName = null,
                                      ?RequestInterface $i_req = null ) : bool {
        return static::control()->softStart( $i_logger, $i_stSessionName, $i_req );
    }


    public static function start( ?LoggerInterface  $i_logger = null, ?string $i_stSessionName = null,
                                  ?RequestInterface $i_req = null ) : bool {
        return static::control()->start( $i_logger, $i_stSessionName, $i_req );
    }


    /**
     * @return void
     *
     * Removes all session data. The session remains active.
     */
    public static function unset() : void {
        static::control()->unset();
    }


    /** @param list<string>|string $i_namespace */
    public static function vars( array|string $i_namespace = [] ) : SessionNamespace {
        if ( ! is_array( $i_namespace ) ) {
            $i_namespace = [ $i_namespace ];
        }
        return new SessionNamespace( static::backend(), $i_namespace );
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
        static::control()->writeClose();
    }


    protected static function backend() : SessionBackendInterface {
        if ( ! self::$backend instanceof SessionBackendInterface ) {
            static::init();
        }
        return self::$backend ?? throw new \LogicException( 'Session backend not initialized.' );
    }


    protected static function clearBackend() : void {
        self::$backend = null;
        self::$control = null;
    }


}
