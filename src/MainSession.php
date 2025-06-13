<?php


declare( strict_types = 1 );


namespace JDWX\Web;


use JDWX\Web\Backends\PHPSessionBackend;
use JDWX\Web\Backends\SessionBackendInterface;
use LogicException;
use Psr\Log\LoggerInterface;
use RuntimeException;


class MainSession implements SessionInterface {


    private const int DEFAULT_LIFETIME_SECONDS = 14400;

    private readonly SessionBackendInterface $backend;


    public function __construct( ?SessionBackendInterface $backend = null,
                                 private readonly int     $uLifetimeSeconds = self::DEFAULT_LIFETIME_SECONDS ) {
        $this->backend = $backend ?? new PHPSessionBackend();
    }


    /**
     * @return void
     *
     * Abort the session without saving any changes to the session data.
     */
    public function abort() : void {
        $this->backend->abortEx();
    }


    /**
     * @return bool True if a session is active, false otherwise.
     */
    public function active() : bool {
        return $this->backend->status() == PHP_SESSION_ACTIVE;
    }


    public function cacheLimiter( ?string $i_nstCacheLimiter = null ) : string {
        return $this->backend->cacheLimiterEx( $i_nstCacheLimiter );
    }


    public function cookieInRequest( ?RequestInterface $i_req = null ) : bool {
        if ( ! $i_req ) {
            $i_req = Request::getGlobal();
        }
        return $i_req->cookieHas( $this->backend->nameEx() );
    }


    public function destroy() : void {
        $this->checkActive();
        $this->backend->destroyEx();
    }


    public function flush() : void {
        $this->checkActive();
        $ntmExpire = $this->getIntOrNull( 'tmExpire' );
        $this->backend->unsetEx();
        if ( is_int( $ntmExpire ) ) {
            $this->set( 'tmExpire', $ntmExpire );
        }
    }


    public function get( string $i_stKey, mixed $i_xDefault = null ) : mixed {

        if ( ! $this->has( $i_stKey ) ) {
            return $i_xDefault;
        }

        return $this->backend->get( $i_stKey );

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
        return $this->backend->has( $i_stKey );
    }


    public function id() : string {
        $this->checkActive();
        return $this->backend->idEx();
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
            $this->backend->set( $i_stKey, $this->backend->get( $i_stKey ) + $i_nValue );
        }
    }


    /** @return array<string, string|list<string>> */
    public function list() : array {
        $this->checkActive();
        return $this->backend->list();
    }


    /**
     * @param string $i_stKey1
     * @param string $i_stKey2
     * @return mixed The found value or null if not found.
     *
     * Get a session variable in a namespaced hierarchy. Note that it is not
     * possible to distinguish between a null value and a non-existent value.
     */
    public function nestedGet( string $i_stKey1, string $i_stKey2 ) : mixed {
        if ( ! $this->nestedHas( $i_stKey1, $i_stKey2 ) ) {
            return null;
        }
        return $this->backend->get2( $i_stKey1, $i_stKey2 );
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
    public function nestedGetInt( string $i_stKey1, string $i_stKey2, ?int $i_niDefault = null ) : int {
        $nst = $this->nestedGetIntOrNull( $i_stKey1, $i_stKey2 );
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
    public function nestedGetIntOrNull( string $i_stKey1, string $i_stKey2 ) : ?int {
        $x = $this->nestedGet( $i_stKey1, $i_stKey2 );
        if ( $x === null ) {
            return null;
        }
        if ( is_int( $x ) ) {
            return $x;
        }
        throw new RuntimeException( "Session value {$i_stKey1}/{$i_stKey2} is not an integer." );
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
    public function nestedGetString( string $i_stKey1, string $i_stKey2, ?string $i_nstDefault = null ) : string {
        $nst = $this->nestedGetStringOrNull( $i_stKey1, $i_stKey2 );
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
    public function nestedGetStringOrNull( string $i_stKey1, string $i_stKey2 ) : ?string {
        $x = $this->nestedGet( $i_stKey1, $i_stKey2 );
        if ( $x === null ) {
            return null;
        }
        if ( is_string( $x ) ) {
            return $x;
        }
        throw new RuntimeException( "Session value {$i_stKey1}/{$i_stKey2} is not a string." );
    }


    public function nestedHas( string $i_stKey1, string $i_stKey2 ) : bool {
        $this->checkActive();
        return $this->backend->has2( $i_stKey1, $i_stKey2 );
    }


    /**
     * @param string $i_stKey1
     * @param string $i_stKey2
     * @param float|int $i_nValue
     * @return void
     *
     * Increment a session variable in a namespaced hierarchy.
     */
    public function nestedIncrement( string $i_stKey1, string $i_stKey2, float|int $i_nValue = 1 ) : void {
        $this->checkActive();
        if ( ! $this->nestedHas( $i_stKey1, $i_stKey2 ) ) {
            $this->nestedSet( $i_stKey1, $i_stKey2, $i_nValue );
        } else {
            $this->backend->set2(
                $i_stKey1, $i_stKey2,
                $this->backend->get2( $i_stKey1, $i_stKey2 ) + $i_nValue
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
    public function nestedRemove( string $i_stKey1, string $i_stKey2 ) : void {
        $this->checkActive();
        $this->backend->remove2( $i_stKey1, $i_stKey2 );
    }


    /**
     * @param string $i_stKey1 The first key.
     * @param string $i_stKey2 The second key.
     * @param mixed $i_xValue The value to set.
     * @return void
     *
     * Simplifies setting a session variable in a two-level hierarchy.
     */
    public function nestedSet( string $i_stKey1, string $i_stKey2, mixed $i_xValue ) : void {
        $this->checkActive();
        if ( ! $this->has( $i_stKey1 ) ) {
            $this->backend->set( $i_stKey1, [] );
        }
        $this->backend->set2( $i_stKey1, $i_stKey2, $i_xValue );
    }


    /**
     * @return array<string, string|list<string>>
     *
     * Return the session data while the session is not active.
     */
    public function peek() : array {
        $this->backend->start();
        $a = $this->backend->list();
        $this->backend->abortEx();
        return $a;
    }


    /**
     * @param bool $i_bDeleteOld
     * @return void
     *
     * Regenerate the session ID while preserving the session data.
     */
    public function regenerate( bool $i_bDeleteOld = false ) : void {
        $this->checkActive();
        $this->backend->regenerateIdEx( $i_bDeleteOld );
    }


    /**
     * @param string $i_stKey
     * @return void
     *
     * Remove a session variable.
     */
    public function remove( string $i_stKey ) : void {
        $this->checkActive();
        $this->backend->remove( $i_stKey );
    }


    /**
     * @return void
     *
     * Reset the session data to its state when the session was started.
     *
     */
    public function reset( bool $i_bPreserveTimes = true ) : void {
        $ntmExpire = $this->getIntOrNull( 'tmExpire' );
        $ntmStart = $this->getIntOrNull( 'tmStart' );
        if ( ! $this->backend->reset() ) {
            throw new RuntimeException( 'Session reset failed.' );
        }
        if ( $i_bPreserveTimes ) {
            if ( is_int( $ntmExpire ) ) {
                $this->set( 'tmExpire', $ntmExpire );
            }
            if ( is_int( $ntmStart ) ) {
                $this->set( 'tmStart', $ntmStart );
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
    public function set( string $i_stKey, mixed $i_xValue ) : void {
        $this->checkActive();
        $this->backend->set( $i_stKey, $i_xValue );
    }


    /**
     * Start a session if one is not already active.
     */
    public function softStart( ?LoggerInterface  $i_logger = null, ?string $i_stSessionName = null,
                               ?RequestInterface $i_req = null ) : bool {
        if ( $this->active() ) {
            return true;
        }
        return $this->start( $i_logger, $i_stSessionName, $i_req );
    }


    /**
     * @param LoggerInterface|null $i_logger
     * @param string|null $i_stSessionName
     * @param RequestInterface|null $i_req
     * @return bool
     *
     * Start a session. If a session is already active, an exception is thrown.
     */
    public function start( ?LoggerInterface  $i_logger = null, ?string $i_stSessionName = null,
                           ?RequestInterface $i_req = null ) : bool {
        if ( is_string( $i_stSessionName ) ) {
            $stSessionName = $i_stSessionName;
            $this->backend->name( $stSessionName );
        } else {
            $stSessionName = $this->backend->nameEx();
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

        if ( $this->active() ) {
            throw new LogicException( 'Session already started.' );
        }

        $this->backend->startEx();

        $ntmExpire = $this->getIntOrNull( 'tmExpire' );
        // error_log( ( $ntmExpire ?? 0 ) . " <?< " . time()
        //    . " " . ( $ntmExpire < time() ? "true" : "false" )
        //    . " " . ( $ntmExpire ? "true" : "false" )
        // );
        if ( $ntmExpire && $ntmExpire < time() ) {
            $i_logger?->info( 'Session expired.' );
            $this->flush();
        } elseif ( $ntmExpire === null ) {
            // $i_logger?->info( 'New session started.' );
            $this->flush();
        } else {
            // $i_logger?->info( "Existing session resumed." );
        }
        $tmNow = time();
        if ( ! $this->has( 'tmStart' ) ) {
            $this->set( 'tmStart', $tmNow );
        }
        // $tmOldExpire = $ntmExpire ?? 0;
        $tmExpire = $tmNow + $this->uLifetimeSeconds;
        $this->set( 'tmExpire', $tmExpire );
        // $i_logger?->info( "Session extended from {$tmOldExpire} to {$tmExpire}" );
        return true;
    }


    /**
     * @return void
     *
     * Removes all session data. The session remains active.
     */
    public function unset() : void {
        $this->checkActive();
        $this->backend->unsetEx();
    }


    /**
     * @return void
     *
     * Write the session data and close the session. Used if you
     * need to do additional processing after writing the session data
     * but don't want to block potential other requests that might
     * need to access the session.
     */
    public function writeClose() : void {
        $this->checkActive();
        $this->backend->writeCloseEx();
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