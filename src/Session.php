<?php /** @noinspection PhpUnused */


declare( strict_types = 1 );


namespace JDWX\Web;


use JDWX\Strict\TypeIs;
use JDWX\Web\Backends\PHPSessionBackend;
use JDWX\Web\Backends\SessionBackendInterface;
use LogicException;
use Psr\Log\LoggerInterface;
use RuntimeException;
use TypeError;


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


    private const int LIFETIME_SECONDS = 14400;

    protected static ?SessionBackendInterface $backend = null;


    /**
     * @return void
     *
     * Abort the session without saving any changes to the session data.
     */
    public static function abort() : void {
        static::backend()->abortEx();
    }


    /**
     * @return bool True if a session is active, false otherwise.
     */
    public static function active() : bool {
        return static::backend()->status() == PHP_SESSION_ACTIVE;
    }


    public static function cacheLimiter( ?string $i_nstCacheLimiter = null ) : string {
        return static::backend()->cacheLimiterEx( $i_nstCacheLimiter );
    }


    /** @deprecated Use Session::remove() */
    public static function clear( string $i_stKey ) : void {
        static::remove( $i_stKey );
    }


    public static function cookieInRequest( ?RequestInterface $i_req = null ) : bool {
        if ( ! $i_req ) {
            $i_req = Request::getGlobal();
        }
        return $i_req->cookieHas( static::backend()->nameEx() );
    }


    public static function destroy() : void {
        static::checkActive();
        static::backend()->destroyEx();
    }


    public static function flush() : void {
        static::checkActive();
        $ntmExpire = static::getIntOrNull( 'tmExpire' );
        static::backend()->unsetEx();
        if ( is_int( $ntmExpire ) ) {
            static::set( 'tmExpire', $ntmExpire );
        }
    }


    public static function get( string $i_stKey, mixed $i_xDefault = null ) : mixed {

        if ( ! static::has( $i_stKey ) ) {
            return $i_xDefault;
        }

        return static::backend()->get( $i_stKey );

    }


    public static function getInt( string $i_stKey, ?int $i_niDefault = null ) : int {
        $x = static::get( $i_stKey );
        if ( is_int( $x ) ) {
            return $x;
        }
        if ( is_int( $i_niDefault ) ) {
            return $i_niDefault;
        }
        throw new RuntimeException( "Session value {$i_stKey} is not found and no default was provided." );
    }


    public static function getIntOrNull( string $i_stKey ) : ?int {
        $x = static::get( $i_stKey );
        if ( $x === null ) {
            return null;
        }
        if ( is_int( $x ) ) {
            return $x;
        }
        throw new TypeError( "Session value {$i_stKey} is not an integer." );
    }


    public static function getString( string $i_stKey, ?string $i_nstDefault = null ) : string {
        $x = static::get( $i_stKey );
        if ( is_string( $x ) ) {
            return $x;
        }
        if ( is_string( $i_nstDefault ) ) {
            return $i_nstDefault;
        }
        throw new RuntimeException( "Session value {$i_stKey} is not found and no default was provided." );
    }


    public static function getStringOrNull( string $i_stKey ) : ?string {
        $x = static::get( $i_stKey );
        if ( $x === null ) {
            return null;
        }
        if ( is_string( $x ) ) {
            return $x;
        }
        throw new TypeError( "Session value {$i_stKey} is not a string." );
    }


    public static function has( string $i_stKey ) : bool {
        static::checkActive();
        return static::backend()->has( $i_stKey );
    }


    public static function id() : string {
        static::checkActive();
        return static::backend()->idEx();
    }


    /**
     * @param string $i_stKey The name of the session variable to increment.
     * @param float|int $i_nValue The value to increment by. (Default: 1)
     */
    public static function increment( string $i_stKey, float|int $i_nValue = 1 ) : void {
        static::checkActive();
        if ( ! static::has( $i_stKey ) ) {
            static::set( $i_stKey, $i_nValue );
        } else {
            static::backend()->set( $i_stKey, static::backend()->get( $i_stKey ) + $i_nValue );
        }
    }


    /**
     * @param SessionBackendInterface $i_backend The session backend to use.
     * @return void
     *
     * Initialize the session handler. Only used for testing.
     */
    public static function init( SessionBackendInterface $i_backend ) : void {
        static::$backend = $i_backend;
    }


    /** @return array<string, string|list<string>> */
    public static function list() : array {
        static::checkActive();
        return static::backend()->list();
    }


    /** @deprecated Use Session::nestedRemove(). */
    public static function nestedClear( string $i_stKey, string $i_stKey2 ) : void {
        static::nestedRemove( $i_stKey, $i_stKey2 );
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
        if ( ! static::nestedHas( $i_stKey1, $i_stKey2 ) ) {
            return null;
        }
        return static::backend()->get2( $i_stKey1, $i_stKey2 );
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
        $nst = static::nestedGetIntOrNull( $i_stKey1, $i_stKey2 );
        if ( is_int( $nst ) ) {
            return $nst;
        }
        if ( is_int( $i_niDefault ) ) {
            return $i_niDefault;
        }
        throw new RuntimeException( "Session value {$i_stKey1}/{$i_stKey2} is not found and no default was provided." );
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
        $x = static::nestedGet( $i_stKey1, $i_stKey2 );
        if ( $x === null ) {
            return null;
        }
        if ( is_int( $x ) ) {
            return $x;
        }
        throw new TypeError( "Session value {$i_stKey1}/{$i_stKey2} is not an integer." );
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
    public static function nestedGetString( string $i_stKey1, string $i_stKey2, ?string $i_nstDefault = null ) : string {
        $nst = static::nestedGetStringOrNull( $i_stKey1, $i_stKey2 );
        if ( is_string( $nst ) ) {
            return $nst;
        }
        if ( is_string( $i_nstDefault ) ) {
            return $i_nstDefault;
        }
        throw new RuntimeException( "Session value {$i_stKey1}/{$i_stKey2} is not found and no default was provided." );
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
        $x = static::nestedGet( $i_stKey1, $i_stKey2 );
        if ( $x === null ) {
            return null;
        }
        if ( is_string( $x ) ) {
            return $x;
        }
        throw new TypeError( "Session value {$i_stKey1}/{$i_stKey2} is not a string." );
    }


    public static function nestedHas( string $i_stKey1, string $i_stKey2 ) : bool {
        static::checkActive();
        return static::backend()->has2( $i_stKey1, $i_stKey2 );
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
        static::checkActive();
        if ( ! static::nestedHas( $i_stKey1, $i_stKey2 ) ) {
            static::nestedSet( $i_stKey1, $i_stKey2, $i_nValue );
        } else {
            static::backend()->set2(
                $i_stKey1, $i_stKey2,
                static::backend()->get2( $i_stKey1, $i_stKey2 ) + $i_nValue
            );
        }
    }


    /**
     * @param string $i_stKey1
     * @param string $i_stKey2
     * @return void
     *
     * Remove a session variable in a namespaced hierarchy.
     */
    public static function nestedRemove( string $i_stKey1, string $i_stKey2 ) : void {
        static::checkActive();
        static::backend()->remove2( $i_stKey1, $i_stKey2 );
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
        static::checkActive();
        if ( ! static::has( $i_stKey1 ) ) {
            static::backend()->set( $i_stKey1, [] );
        }
        static::backend()->set2( $i_stKey1, $i_stKey2, $i_xValue );
    }


    /**
     * @return array<string, string|list<string>>
     *
     * Return the session data while the session is not active.
     */
    public static function peek() : array {
        static::backend()->start();
        $a = static::backend()->list();
        static::backend()->abortEx();
        return $a;
    }


    /**
     * @param bool $i_bDeleteOld
     * @return void
     *
     * Regenerate the session ID while preserving the session data.
     */
    public static function regenerate( bool $i_bDeleteOld = false ) : void {
        static::checkActive();
        static::backend()->regenerateIdEx( $i_bDeleteOld );
    }


    /**
     * @param string $i_stKey
     * @return void
     *
     * Remove a session variable.
     */
    public static function remove( string $i_stKey ) : void {
        static::checkActive();
        static::backend()->remove( $i_stKey );
    }


    /**
     * @return void
     *
     * Reset the session data to its state when the session was started.
     *
     */
    public static function reset( bool $i_bPreserveTimes = true ) : void {
        $ntmExpire = static::getIntOrNull( 'tmExpire' );
        $ntmStart = static::getIntOrNull( 'tmStart' );
        if ( ! static::backend()->reset() ) {
            throw new RuntimeException( 'Session reset failed.' );
        }
        if ( $i_bPreserveTimes ) {
            if ( is_int( $ntmExpire ) ) {
                static::set( 'tmExpire', $ntmExpire );
            }
            if ( is_int( $ntmStart ) ) {
                static::set( 'tmStart', $ntmStart );
            }
        }
    }


    /**
     * @param string $i_stKey The name of the session variable to set.
     * @param mixed $i_xValue The value to set.
     * @return void
     *
     * Set a session variable.
     */
    public static function set( string $i_stKey, mixed $i_xValue ) : void {
        static::checkActive();
        static::backend()->set( $i_stKey, $i_xValue );
    }


    /**
     * Start a session if one is not already active.
     */
    public static function softStart( ?LoggerInterface  $i_logger = null, ?string $i_stSessionName = null,
                                      ?RequestInterface $i_req = null ) : bool {
        if ( static::active() ) {
            return true;
        }
        return static::start( $i_logger, $i_stSessionName, $i_req );
    }


    /**
     * @param LoggerInterface|null $i_logger
     * @param string|null $i_stSessionName
     * @param RequestInterface|null $i_req
     * @return bool
     *
     * Start a session. If a session is already active, an exception is thrown.
     */
    public static function start( ?LoggerInterface  $i_logger = null, ?string $i_stSessionName = null,
                                  ?RequestInterface $i_req = null ) : bool {
        if ( is_string( $i_stSessionName ) ) {
            $stSessionName = $i_stSessionName;
            static::backend()->name( $stSessionName );
        } else {
            $stSessionName = static::backend()->nameEx();
        }

        if ( ! $i_req ) {
            $i_req = Request::getGlobal();
        }
        if ( $i_req->cookieHas( $stSessionName ) ) {
            $sid = $i_req->cookieEx( $stSessionName )->asString();
            if ( ! preg_match( '/^[-a-zA-Z0-9,]+$/', $sid ) ) {
                $i_logger?->warning( "Bogus characters in session cookie: {$sid}" );
                return false;
            }
            if ( strlen( $sid ) > 40 ) {
                $i_logger?->warning( "Session cookie is too long: {$sid}" );
                return false;
            }
        }

        if ( static::active() ) {
            throw new LogicException( 'Session already started.' );
        }

        static::backend()->startEx();

        $ntmExpire = static::getIntOrNull( 'tmExpire' );
        // error_log( ( $ntmExpire ?? 0 ) . " <?< " . time()
        //    . " " . ( $ntmExpire < time() ? "true" : "false" )
        //    . " " . ( $ntmExpire ? "true" : "false" )
        // );
        if ( $ntmExpire && $ntmExpire < time() ) {
            $i_logger?->info( 'Session expired.' );
            static::flush();
        } elseif ( $ntmExpire === null ) {
            // $i_logger?->info( 'New session started.' );
            static::flush();
        } else {
            // $i_logger?->info( "Existing session resumed." );
        }
        $tmNow = time();
        if ( ! static::has( 'tmStart' ) ) {
            static::set( 'tmStart', $tmNow );
        }
        // $tmOldExpire = $ntmExpire ?? 0;
        $tmExpire = $tmNow + self::LIFETIME_SECONDS;
        static::set( 'tmExpire', $tmExpire );
        // $i_logger?->info( "Session extended from {$tmOldExpire} to {$tmExpire}" );
        return true;
    }


    /**
     * @return void
     *
     * Removes all session data. The session remains active.
     */
    public static function unset() : void {
        static::checkActive();
        static::backend()->unsetEx();
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
        static::checkActive();
        static::backend()->writeCloseEx();
    }


    protected static function backend() : SessionBackendInterface {
        if ( ! static::$backend instanceof SessionBackendInterface ) {
            static::init( new PHPSessionBackend() );
        }
        return TypeIs::object( static::$backend );
    }


    /**
     * @return void
     *
     * Check if a session is active and throw an exception if it is not.
     */
    protected static function checkActive() : void {
        if ( ! static::active() ) {
            throw new LogicException( 'Session not started.' );
        }
    }


}
