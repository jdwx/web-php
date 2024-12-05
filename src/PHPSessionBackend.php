<?php


declare( strict_types = 1 );


namespace JDWX\Web;


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


    public function clear( string $name ) : void {
        unset( $_SESSION[ $name ] );
    }


    public function clear2( string $name, string $sub ) : void {
        unset( $_SESSION[ $name ][ $sub ] );
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


    public function get( string $name ) : mixed {
        return $_SESSION[ $name ];
    }


    public function get2( string $name, string $sub ) : mixed {
        return $_SESSION[ $name ][ $sub ];
    }


    /** @return array<string, int|bool|string> */
    public function getCookieParams() : array {
        return session_get_cookie_params();
    }


    public function has( string $name ) : bool {
        return array_key_exists( $name, $_SESSION );
    }


    public function has2( string $name, string $sub ) : bool {
        return array_key_exists( $name, $_SESSION ) && array_key_exists( $sub, $_SESSION[ $name ] );
    }


    /** @suppress PhanTypeMismatchArgumentNullableInternal */
    public function id( ?string $id = null ) : string|false {
        return session_id( $id );
    }


    /** @return array<string, string|list<string>> */
    public function list() : array {
        # This forces a copy so we don't hand back a modifiable reference to $_SESSION.
        return array_merge( [], $_SESSION );
    }


    public function moduleName() : string|false {
        return session_module_name();
    }


    /** @suppress PhanTypeMismatchArgumentNullableInternal */
    public function name( ?string $value = null ) : string|false {
        return session_name( $value );
    }


    /** @suppress PhanTypeMismatchArgumentNullableInternal */
    public function regenerateId( ?bool $deleteOldSession = false ) : bool {
        return session_regenerate_id( $deleteOldSession );
    }


    public function registerShutdown() : void {
        session_register_shutdown();
    }


    public function reset() : bool {
        return session_reset();
    }


    /** @suppress PhanTypeMismatchArgumentNullableInternal */
    public function savePath( ?string $value = null ) : string|false {
        return session_save_path( $value );
    }


    public function set( string $name, mixed $value ) : void {
        $_SESSION[ $name ] = $value;
    }


    public function set2( string $name, string $sub, mixed $value ) : void {
        $_SESSION[ $name ][ $sub ] = $value;
    }


    public function setCookieParams( int  $lifetime, string $path = '', string $domain = '',
                                     bool $secure = false, bool $httponly = false ) : bool {
        return session_set_cookie_params( $lifetime, $path, $domain, $secure, $httponly );
    }


    /** @suppress PhanTypeMismatchArgumentNullableInternal */
    public function setSaveHandler( callable  $open, callable $close, callable $read, callable $write,
                                    callable  $destroy, callable $gc, ?callable $create_sid = null,
                                    ?callable $validate_sid = null, ?callable $update_timestamp = null ) : bool {
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


}
