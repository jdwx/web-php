<?php


declare( strict_types = 1 );


namespace JDWX\Web\Backends;


/**
 * Use the real PHP functions needed for session handling.
 *
 * @codeCoverageIgnore
 */
class PHPSessionBackend extends AbstractSessionBackend {


    public function abort() : bool {
        return session_abort();
    }


    /** @suppress PhanTypeMismatchArgumentNullableInternal */
    public function cacheExpire( ?int $value = null ) : int|false {
        return session_cache_expire( $value );
    }


    /** @suppress PhanTypeMismatchArgumentNullableInternal */
    public function cacheLimiter( ?string $value = null ) : string|false {
        return session_cache_limiter( $value );
    }


    public function createId( string $prefix = '' ) : string|false {
        return session_create_id( $prefix );
    }


    public function decode( string $data ) : bool {
        return session_decode( $data );
    }


    public function destroy() : bool {
        return session_destroy();
    }


    public function encode() : string|false {
        return session_encode();
    }


    public function gc() : int|false {
        return session_gc();
    }


    /** @param list<string> $namespace */
    public function get( array $namespace, string $name ) : mixed {
        $r = $this->getNamespace( $namespace );
        return $r[ $name ];
    }


    /** @return array<string, int|bool|string> */
    public function getCookieParams() : array {
        return session_get_cookie_params();
    }


    public function has( array $namespace, string $name ) : bool {
        $r = $this->getNamespace( $namespace );
        return isset( $r[ $name ] );
    }


    /** @suppress PhanTypeMismatchArgumentNullableInternal */
    public function id( ?string $id = null ) : string|false {
        return session_id( $id );
    }


    /**
     * @param list<string> $namespace
     * @return array<string, string|list<string>>
     */
    public function list( array $namespace ) : array {
        $r = $this->getNamespace( $namespace );
        # This forces a copy so we don't hand back a modifiable reference to $_SESSION.
        return array_merge( [], $r );
    }


    public function moduleName() : string|false {
        return session_module_name();
    }


    /** @suppress PhanTypeMismatchArgumentNullableInternal */
    public function name( ?string $value = null ) : string|false {
        return session_name( $value );
    }


    public function regenerateId( bool $deleteOldSession = false ) : bool {
        return session_regenerate_id( $deleteOldSession );
    }


    public function registerShutdown() : void {
        session_register_shutdown();
    }


    /** @param list<string> $namespace */
    public function remove( array $namespace, string $name ) : void {
        $r = &$this->getNamespace( $namespace );
        unset( $r[ $name ] );
    }


    public function reset() : bool {
        return session_reset();
    }


    /** @suppress PhanTypeMismatchArgumentNullableInternal */
    public function savePath( ?string $value = null ) : string|false {
        return session_save_path( $value );
    }


    public function set( array $namespace, string $name, mixed $value ) : void {
        $r = &$this->getNamespace( $namespace );
        $r[ $name ] = $value;
    }


    public function setCookieParams( int  $lifetime, string $path = '', string $domain = '',
                                     bool $secure = false, bool $httponly = false ) : bool {
        return session_set_cookie_params( $lifetime, $path, $domain, $secure, $httponly );
    }


    /**
     * @param callable(): string|null $create_sid
     * @suppress PhanTypeMismatchArgumentNullableInternal
     */
    public function setSaveHandler( callable  $open, callable $close, callable $read, callable $write,
                                    callable  $destroy, callable $gc, ?callable $create_sid = null,
                                    ?callable $validate_sid = null, ?callable $update_timestamp = null ) : bool {
        if ( ! is_callable( $create_sid ) ) {
            return session_set_save_handler( $open, $close, $read, $write, $destroy, $gc );
        }
        if ( ! is_callable( $validate_sid ) ) {
            return session_set_save_handler( $open, $close, $read, $write, $destroy, $gc,
                $create_sid );
        }
        if ( ! is_callable( $update_timestamp ) ) {
            return session_set_save_handler( $open, $close, $read, $write, $destroy, $gc,
                $create_sid, $validate_sid );
        }
        return session_set_save_handler( $open, $close, $read, $write, $destroy, $gc, $create_sid,
            $validate_sid, $update_timestamp );
    }


    /** @param array<string, int|string> $options */
    public function start( array $options = [] ) : bool {
        return session_start( $options );
    }


    public function status() : int {
        return session_status();
    }


    public function unset() : bool {
        return session_unset();
    }


    public function writeClose() : bool {
        return session_write_close();
    }


    /**
     * @param list<string> $namespace
     * @return array<string, mixed>
     */
    protected function & getNamespace( array $namespace ) : array {
        $r = &$_SESSION;
        foreach ( $namespace as $stKey ) {
            if ( ! array_key_exists( $stKey, $r ) ) {
                $r[ $stKey ] = [];
            }
            $r = &$r[ $stKey ];
        }
        return $r;
    }


}
