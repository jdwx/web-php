<?php


declare( strict_types = 1 );


namespace JDWX\Web;


use JDWX\Web\Backends\SessionBackendInterface;
use LogicException;
use Psr\Log\LoggerInterface;
use RuntimeException;


/**
 * Class SessionControl
 *
 * This class provides methods to control session behavior, such as starting,
 * destroying, and configuring sessions. I.e., all the functionality that
 * applies to the session as a whole, not to individual session variables.
 *
 * @package JDWX\Web
 */
class SessionControl extends SessionBase {


    private const int DEFAULT_LIFETIME_SECONDS = 14400;

    private static SessionControl $instance;

    protected readonly int $uLifetimeSeconds;


    /**
     * @param SessionBackendInterface|null $backend The session backend to use, or null for the default PHP backend.
     * @param int|null $nuLifetimeSeconds The session lifetime in seconds, or null for the default (4 hours).
     */
    public function __construct( ?SessionBackendInterface $backend = null,
                                 ?int                     $nuLifetimeSeconds = null ) {
        parent::__construct( $backend );
        $this->uLifetimeSeconds = $nuLifetimeSeconds ?? self::DEFAULT_LIFETIME_SECONDS;
    }


    /**
     * Returns the global SessionControl singleton, creating it with default
     * settings if it has not been initialized.
     */
    public static function getGlobal() : self {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    /**
     * Sets the global SessionControl singleton. If a SessionBackendInterface or null
     * is provided, a new SessionControl instance is created wrapping it.
     *
     * @param self|SessionBackendInterface|null $backend A SessionControl instance to use directly,
     *        a backend to wrap in a new SessionControl, or null for the default backend.
     * @param int|null $nuLifetimeSeconds Session lifetime in seconds (only used when $backend is not a SessionControl).
     */
    public static function setGlobal( self|SessionBackendInterface|null $backend,
                                      ?int                              $nuLifetimeSeconds = null ) : void {
        if ( ! $backend instanceof self ) {
            $backend = new self( $backend, $nuLifetimeSeconds );
        }
        self::$instance = $backend;
    }


    /**
     * Aborts the session without saving any changes to the session data.
     * The session is closed and in-memory modifications are discarded.
     */
    public function abort() : void {
        $this->backend->abortEx();
    }


    /** Returns the underlying session backend implementation. */
    public function backend() : SessionBackendInterface {
        return $this->backend;
    }


    /**
     * Gets or sets the cache limiter for the session.
     *
     * @param string|null $i_nstCacheLimiter The cache limiter to set (e.g. "nocache", "public",
     *        "private", "private_no_expire"), or null to retrieve the current value.
     * @return string The current (or newly set) cache limiter value.
     */
    public function cacheLimiter( ?string $i_nstCacheLimiter = null ) : string {
        return $this->backend->cacheLimiterEx( $i_nstCacheLimiter );
    }


    /**
     * Checks whether the current request contains a session cookie, indicating
     * that the client has an existing session.
     *
     * @param RequestInterface|null $i_req The request to check, or null to use the global request.
     * @return bool True if the request contains a cookie matching the session name.
     */
    public function cookieInRequest( ?RequestInterface $i_req = null ) : bool {
        if ( ! $i_req ) {
            $i_req = Request::getGlobal();
        }
        return $i_req->cookieHas( $this->backend->nameEx() );
    }


    /**
     * Destroys the current session and its data.
     *
     * @throws LogicException If no session is active.
     */
    public function destroy() : void {
        $this->checkActive();
        $this->backend->destroyEx();
    }


    /**
     * Clears all session data while preserving the expiration timestamp.
     * The session remains active after flushing.
     *
     * @throws LogicException If no session is active.
     */
    public function flush() : void {
        $this->checkActive();
        $vars = $this->namespace();
        $ntmExpire = $vars->getIntOrNull( 'tmExpire' );
        $this->backend->unsetEx();
        if ( is_int( $ntmExpire ) ) {
            $vars->set( 'tmExpire', $ntmExpire );
        }
    }


    /**
     * Returns the current session ID.
     *
     * @throws LogicException If no session is active.
     */
    public function id() : string {
        $this->checkActive();
        return $this->backend->idEx();
    }


    /** Returns the configured session lifetime in seconds. */
    public function lifetime() : int {
        return $this->uLifetimeSeconds;
    }


    /**
     * Creates a SessionNamespace for accessing session variables within
     * a given namespace.
     *
     * @param list<string>|string $i_namespace The namespace path, either as a list of
     *        segments or a single string. An empty array accesses the root namespace.
     */
    public function namespace( array|string $i_namespace = [] ) : SessionNamespace {
        return new SessionNamespace( $this->backend, $i_namespace );
    }


    /**
     * Reads session data without keeping the session open. Briefly starts
     * the session, reads the data, and then aborts (discarding any changes)
     * to avoid holding the session lock.
     *
     * @param list<string> $namespace The namespace to read from (empty for root).
     * @return array<string, string|list<string>> The session data in the given namespace.
     */
    public function peek( array $namespace = [] ) : array {
        $this->backend->start();
        $a = $this->backend->list( $namespace );
        $this->backend->abortEx();
        return $a;
    }


    /**
     * Regenerates the session ID while preserving all session data.
     * This is useful for preventing session fixation attacks after
     * privilege changes (e.g., login).
     *
     * @param bool $i_bDeleteOld If true, the old session file is deleted.
     * @throws LogicException If no session is active.
     */
    public function regenerate( bool $i_bDeleteOld = false ) : void {
        $this->checkActive();
        $this->backend->regenerateIdEx( $i_bDeleteOld );
    }


    /**
     * Resets the session data to the state it was in when the session was
     * last started (i.e., re-reads from storage, discarding in-memory changes).
     *
     * @param bool $i_bPreserveTimes If true (default), the tmExpire and tmStart
     *        timestamps are preserved across the reset.
     * @throws RuntimeException If the session reset fails.
     */
    public function reset( bool $i_bPreserveTimes = true ) : void {
        $vars = $this->namespace();
        $ntmExpire = $vars->getIntOrNull( 'tmExpire' );
        $ntmStart = $vars->getIntOrNull( 'tmStart' );
        if ( ! $this->backend->reset() ) {
            throw new RuntimeException( 'Session reset failed.' );
        }
        if ( $i_bPreserveTimes ) {
            if ( is_int( $ntmExpire ) ) {
                $vars->set( 'tmExpire', $ntmExpire );
            }
            if ( is_int( $ntmStart ) ) {
                $vars->set( 'tmStart', $ntmStart );
            }
        }
    }


    /**
     * Starts a session only if one is not already active. This is a no-op
     * convenience wrapper around start() that avoids the LogicException
     * thrown when a session is already running.
     *
     * @param LoggerInterface|null $i_logger Logger for session warnings/info (e.g., bogus cookies, expiry).
     * @param string|null $i_stSessionName Custom session name or null to use the current/default name.
     * @param RequestInterface|null $i_req The request to read session cookies from, or null for the global request.
     * @return bool True if a session is active (whether it was already running or newly started),
     *              false if session start was rejected (e.g., invalid session cookie).
     */
    public function softStart( ?LoggerInterface  $i_logger = null, ?string $i_stSessionName = null,
                               ?RequestInterface $i_req = null ) : bool {
        if ( $this->active() ) {
            return true;
        }
        return $this->start( $i_logger, $i_stSessionName, $i_req );
    }


    /**
     * Starts a new session. Validates the session cookie (if present) for
     * suspicious characters and excessive length. Handles session expiry by
     * flushing expired sessions and initializes tmStart/tmExpire timestamps.
     *
     * @param LoggerInterface|null $i_logger Logger for session warnings (bogus cookie, expiry).
     * @param string|null $i_stSessionName Custom session name or null to use the current/default name.
     * @param RequestInterface|null $i_req The request to read session cookies from, or null for the global request.
     * @return bool True if the session was started successfully, false if the session
     *              cookie was rejected (invalid characters or too long).
     * @throws LogicException If a session is already active.
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

        $vars = $this->namespace();
        $ntmExpire = $vars->getIntOrNull( 'tmExpire' );
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
        if ( ! $vars->has( 'tmStart' ) ) {
            $vars->set( 'tmStart', $tmNow );
        }
        // $tmOldExpire = $ntmExpire ?? 0;
        $tmExpire = $tmNow + $this->uLifetimeSeconds;
        $vars->set( 'tmExpire', $tmExpire );
        // $i_logger?->info( "Session extended from {$tmOldExpire} to {$tmExpire}" );
        return true;
    }


    /**
     * Removes all session data. The session itself remains active
     * (unlike destroy(), which ends the session entirely).
     *
     * @throws LogicException If no session is active.
     */
    public function unset() : void {
        $this->checkActive();
        $this->backend->unsetEx();
    }


    /**
     * Writes the session data and closes the session. This releases the
     * session lock so that concurrent requests from the same client are
     * not blocked while this request continues.
     *
     * @throws LogicException If no session is active.
     */
    public function writeClose() : void {
        $this->checkActive();
        $this->backend->writeCloseEx();
    }


}
