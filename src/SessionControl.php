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

    protected readonly int $uLifetimeSeconds;


    public function __construct( ?SessionBackendInterface $backend = null,
                                 ?int                     $nuLifetimeSeconds = null ) {
        parent::__construct( $backend );
        $this->uLifetimeSeconds = $nuLifetimeSeconds ?? self::DEFAULT_LIFETIME_SECONDS;
    }


    /**
     * @return void
     *
     * Abort the session without saving any changes to the session data.
     */
    public function abort() : void {
        $this->backend->abortEx();
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
        $vars = $this->namespace();
        $ntmExpire = $vars->getIntOrNull( 'tmExpire' );
        $this->backend->unsetEx();
        if ( is_int( $ntmExpire ) ) {
            $vars->set( 'tmExpire', $ntmExpire );
        }
    }


    public function id() : string {
        $this->checkActive();
        return $this->backend->idEx();
    }


    /** @param list<string>|string $i_namespace */
    public function namespace( array|string $i_namespace = [] ) : SessionNamespace {
        return new SessionNamespace( $this->backend, $i_namespace );
    }


    /**
     * @param list<string> $namespace
     * @return array<string, string|list<string>>
     *
     * Return the session data while the session is not active.
     */
    public function peek( array $namespace = [] ) : array {
        $this->backend->start();
        $a = $this->backend->list( $namespace );
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
     * @return void
     *
     * Reset the session data to its state when the session was started.
     *
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


}
