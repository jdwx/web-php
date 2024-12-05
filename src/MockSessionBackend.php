<?php


declare( strict_types = 1 );


namespace JDWX\Web;


use InvalidArgumentException;
use LogicException;


class MockSessionBackend extends AbstractSessionBackend {


    public bool $bActive;

    public string $stName;

    public string $stCacheLimiter;

    public string $stID;

    /** @var array<string, mixed> */
    public array $rSession;

    public string $stNewID;

    public bool $bFailAbort = false;

    public bool $bFailCacheLimiter = false;

    public bool $bFailDestroy = false;

    public bool $bFailId = false;

    public bool $bFailRegenerate = false;

    public bool $bFailStart = false;

    public bool $bFailUnset = false;

    public bool $bFailWriteClose = false;


    /** @param array<string, string> $rBackup */
    public function __construct( private array $rBackup ) {
        $this->setup();
    }


    public function abort() : bool {
        if ( $this->bFailAbort ) {
            return false;
        }
        $this->bActive = false;
        $this->rSession = $this->rBackup;
        return true;
    }


    public function cacheExpire( ?int $value = null ) : int|false {
        throw new LogicException( 'Not implemented.' );
    }


    public function cacheLimiter( ?string $value = null ) : string|false {
        if ( $this->bFailCacheLimiter ) {
            return false;
        }
        if ( is_string( $value ) ) {
            if ( ! in_array( $value, [ 'nocache', 'public', 'private_no_expire', 'private', 'must_revalidate' ] ) ) {
                throw new InvalidArgumentException( 'Invalid cache limiter.' );
            }
            $this->stCacheLimiter = $value;
        }
        return $this->stCacheLimiter;
    }


    public function clear( string $name ) : void {
        unset( $this->rSession[ $name ] );
    }


    public function clear2( string $name, string $sub ) : void {
        unset( $this->rSession[ $name ][ $sub ] );
    }


    public function createId( string $prefix = '' ) : string|false {
        throw new LogicException( 'Not implemented.' );
    }


    public function decode( string $data ) : bool {
        throw new LogicException( 'Not implemented.' );
    }


    public function destroy() : bool {
        if ( $this->bFailDestroy || ! $this->bActive ) {
            return false;
        }
        $this->bActive = false;
        return true;
    }


    public function encode() : string|false {
        throw new LogicException( 'Not implemented.' );
    }


    public function gc() : int|false {
        throw new LogicException( 'Not implemented.' );
    }


    public function get( string $name ) : mixed {
        return $this->rSession[ $name ];
    }


    public function get2( string $name, string $sub ) : mixed {
        return $this->rSession[ $name ][ $sub ];
    }


    /** @return array<string, int|bool|string> */
    public function getCookieParams() : array {
        throw new LogicException( 'Not implemented.' );
    }


    public function has( string $name ) : bool {
        return array_key_exists( $name, $this->rSession );
    }


    public function has2( string $name, string $sub ) : bool {
        return array_key_exists( $name, $this->rSession ) && array_key_exists( $sub, $this->rSession[ $name ] );
    }


    public function id( ?string $id = null ) : string|false {
        if ( $this->bFailId ) {
            return false;
        }
        if ( is_string( $id ) ) {
            if ( $this->bActive ) {
                throw new LogicException( 'Session already started.' );
            }
            $this->stNewID = $id;
        }
        return $this->stID;
    }


    /** @return array<string, string|list<string>> */
    public function list() : array {
        # This forces a copy so we don't hand back a modifiable reference to $_SESSION.
        return array_merge( [], $this->rSession );
    }


    public function moduleName() : string|false {
        throw new LogicException( 'Not implemented.' );
    }


    public function name( ?string $value = null ) : string|false {
        if ( is_string( $value ) ) {
            $this->stName = $value;
        }
        return $this->stName;
    }


    public function regenerateId( ?bool $deleteOldSession = false ) : bool {
        if ( $this->bFailRegenerate ) {
            return false;
        }
        $this->stID = 'regenerated-id';
        return true;
    }


    public function registerShutdown() : void {
        throw new LogicException( 'Not implemented.' );
    }


    public function reset() : bool {
        throw new LogicException( 'Not implemented.' );
    }


    public function savePath( ?string $value = null ) : string|false {
        throw new LogicException( 'Not implemented.' );
    }


    public function set( string $name, mixed $value ) : void {
        $this->rSession[ $name ] = $value;
    }


    public function set2( string $name, string $sub, mixed $value ) : void {
        $this->rSession[ $name ][ $sub ] = $value;
    }


    public function setCookieParams( int  $lifetime, string $path = '', string $domain = '',
                                     bool $secure = false, bool $httponly = false ) : bool {
        throw new LogicException( 'Not implemented.' );
    }


    public function setSaveHandler( callable  $open, callable $close, callable $read, callable $write,
                                    callable  $destroy, callable $gc, ?callable $create_sid = null,
                                    ?callable $validate_sid = null, ?callable $update_timestamp = null ) : bool {
        throw new LogicException( 'Not implemented.' );
    }


    public function setup() : void {
        $this->bActive = false;
        $this->stName = 'test-session';
        $this->stCacheLimiter = 'nocache';
        $this->stID = '';
        $this->rSession = $this->rBackup;
        $this->stNewID = 'test-id';
    }


    /** @param array<string, int|string> $options */
    public function start( array $options = [] ) : bool {
        if ( $this->bFailStart ) {
            return false;
        }
        if ( $this->bActive ) {
            throw new LogicException( 'Session already started.' );
        }
        $this->bActive = true;
        $this->stID = $this->stNewID;
        return true;
    }


    public function status() : int {
        return $this->bActive
            ? PHP_SESSION_ACTIVE
            : PHP_SESSION_NONE;
    }


    public function unset() : bool {
        if ( $this->bFailUnset ) {
            return false;
        }
        if ( ! $this->bActive ) {
            throw new LogicException( 'Session not started.' );
        }
        $this->rSession = [];
        return true;
    }


    public function writeClose() : bool {
        if ( $this->bFailWriteClose ) {
            return false;
        }
        $this->rBackup = $this->rSession;
        $this->bActive = false;
        return true;
    }


}
