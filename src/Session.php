<?php /** @noinspection PhpUnused */


declare( strict_types = 1 );


namespace JDWX\Web;


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
 */
class Session {


    private const int LIFETIME_SECONDS = 14400;

    protected static ?SessionBackendInterface $backend = null;


    public static function abort() : void {
        static::backend()->abortEx();
    }


    public static function active() : bool {
        return static::backend()->status() == PHP_SESSION_ACTIVE;
    }


    public static function cacheLimiter( ?string $i_nstCacheLimiter = null ) : string {
        return static::backend()->cacheLimiterEx( $i_nstCacheLimiter );
    }


    public static function clear( string $i_stKey ) : void {
        static::checkActive();
        static::backend()->clear( $i_stKey );
    }


    public static function cookieInRequest( ?RequestInterface $i_req = null ) : bool {
        if ( ! $i_req ) {
            $i_req = Request::getGlobal();
        }
        return $i_req->cookieHas( static::backend()->name() );
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


    public static function get( string $i_stKey ) : mixed {

        if ( ! static::has( $i_stKey ) ) {
            return null;
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


    public static function increment( string $i_stKey, float|int $i_nValue = 1 ) : void {
        static::checkActive();
        if ( ! static::has( $i_stKey ) ) {
            static::set( $i_stKey, $i_nValue );
        } else {
            static::backend()->set( $i_stKey, static::backend()->get( $i_stKey ) + $i_nValue );
        }
    }


    public static function init( SessionBackendInterface $i_backend ) : void {
        static::$backend = $i_backend;
    }


    /** @return array<string, string|list<string>> */
    public static function list() : array {
        static::checkActive();
        return static::backend()->list();
    }


    public static function nestedClear( string $i_stKey, string $i_stKey2 ) : void {
        static::checkActive();
        if ( ! static::nestedHas( $i_stKey, $i_stKey2 ) ) {
            return;
        }
        static::backend()->clear2( $i_stKey, $i_stKey2 );
    }


    public static function nestedGet( string $i_stKey1, string $i_stKey2 ) : mixed {
        if ( ! static::nestedHas( $i_stKey1, $i_stKey2 ) ) {
            return null;
        }
        return static::backend()->get2( $i_stKey1, $i_stKey2 );
    }


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


    public static function nestedSet( string $i_stKey1, string $i_stKey2, mixed $i_xValue ) : void {
        static::checkActive();
        if ( ! static::has( $i_stKey1 ) ) {
            static::backend()->set( $i_stKey1, [] );
        }
        static::backend()->set2( $i_stKey1, $i_stKey2, $i_xValue );
    }


    /** @return array<string, string|list<string>> */
    public static function peek() : array {
        static::backend()->start();
        $a = static::backend()->list();
        static::backend()->abortEx();
        return $a;
    }


    public static function regenerate( bool $i_bDeleteOld = false ) : void {
        static::checkActive();
        static::backend()->regenerateIdEx( $i_bDeleteOld );
    }


    public static function set( string $i_stKey, mixed $i_xValue ) : void {
        static::checkActive();
        static::backend()->set( $i_stKey, $i_xValue );
    }


    public static function start( ?LoggerInterface $i_logger = null, ?string $i_stSessionName = null,
                                  ?Request         $i_req = null ) : bool {
        if ( is_string( $i_stSessionName ) ) {
            $stSessionName = $i_stSessionName;
            static::backend()->name( $stSessionName );
        } else {
            $stSessionName = static::backend()->name();
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


    public static function unset() : void {
        static::checkActive();
        static::backend()->unsetEx();
    }


    public static function writeClose() : void {
        static::checkActive();
        static::backend()->writeCloseEx();
    }


    protected static function backend() : SessionBackendInterface {
        if ( ! static::$backend instanceof SessionBackendInterface ) {
            static::init( new PHPSessionBackend() );
        }
        return static::$backend;
    }


    protected static function checkActive() : void {
        if ( ! static::active() ) {
            throw new LogicException( 'Session not started.' );
        }
    }


}
