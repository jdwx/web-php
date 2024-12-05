<?php


declare( strict_types = 1 );


namespace JDWX\Web;


interface ISessionBackend {


    public function abort() : bool;


    public function abortEx() : void;


    public function cacheExpire( ?int $value = null ) : int|false;


    public function cacheLimiter( ?string $value = null ) : string|false;


    public function cacheLimiterEx( ?string $value = null ) : string;


    public function clear( string $name ) : void;


    public function clear2( string $name, string $sub ) : void;


    public function createId( string $prefix = '' ) : string|false;


    public function decode( string $data ) : bool;


    public function destroy() : bool;


    public function destroyEx() : void;


    public function encode() : string|false;


    public function gc() : int|false;


    public function get( string $name ) : mixed;


    public function get2( string $name, string $sub ) : mixed;


    /** @return array<string, int|bool|string> */
    public function getCookieParams() : array;


    public function has( string $name ) : bool;


    public function has2( string $name, string $sub ) : bool;


    public function id( ?string $id = null ) : string|false;


    public function idEx() : string;


    /** @return array<string, string|list<string>> */
    public function list() : array;


    public function moduleName() : string|false;


    public function name( ?string $value = null ) : string|false;


    public function regenerateId( bool $deleteOldSession = false ) : bool;


    public function regenerateIdEx( bool $deleteOldSession = false ) : void;


    public function registerShutdown() : void;


    public function reset() : bool;


    public function savePath( ?string $value = null ) : string|false;


    public function set( string $name, mixed $value ) : void;


    public function set2( string $name, string $sub, mixed $value ) : void;


    public function setCookieParams( int  $lifetime, string $path = '', string $domain = '',
                                     bool $secure = false, bool $httponly = false ) : bool;


    public function setSaveHandler( callable  $open, callable $close, callable $read, callable $write,
                                    callable  $destroy, callable $gc, ?callable $create_sid = null,
                                    ?callable $validate_sid = null, ?callable $update_timestamp = null ) : bool;


    /** @param array<string, int|string> $options */
    public function start( array $options = [] ) : bool;


    /** @param array<string, int|string> $options */
    public function startEx( array $options = [] ) : void;


    public function status() : int;


    public function unset() : bool;


    public function unsetEx() : void;


    public function writeClose() : bool;


    public function writeCloseEx() : void;


}