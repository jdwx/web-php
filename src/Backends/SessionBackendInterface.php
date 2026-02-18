<?php


declare( strict_types = 1 );


namespace JDWX\Web\Backends;


interface SessionBackendInterface {


    public function abort() : bool;


    public function abortEx() : void;


    public function cacheExpire( ?int $value = null ) : int|false;


    public function cacheLimiter( ?string $value = null ) : string|false;


    public function cacheLimiterEx( ?string $value = null ) : string;


    public function clear( array|string $i_namespace = [] ) : void;


    public function createId( string $prefix = '' ) : string|false;


    public function decode( string $data ) : bool;


    public function destroy() : bool;


    public function destroyEx() : void;


    public function encode() : string|false;


    public function gc() : int|false;


    /** @param list<string> $namespace */
    public function get( array $namespace, string $name ) : mixed;


    /** @return array<string, int|bool|string> */
    public function getCookieParams() : array;


    /** @param list<string> $namespace */
    public function has( array $namespace, string $name ) : bool;


    /**
     * @param list<string> $namespace
     * @param list<string> $subNamespace
     */
    public function hasNamespace( array $namespace, array $subNamespace ) : bool;


    public function id( ?string $id = null ) : string|false;


    public function idEx() : string;


    /**
     * @param list<string> $namespace
     * @return array<string, string|list<string>>
     */
    public function list( array $namespace ) : array;


    public function moduleName() : string|false;


    public function name( ?string $value = null ) : string|false;


    public function nameEx( ?string $value = null ) : string;


    public function regenerateId( bool $deleteOldSession = false ) : bool;


    public function regenerateIdEx( bool $deleteOldSession = false ) : void;


    public function registerShutdown() : void;


    /** @param list<string> $namespace */
    public function remove( array $namespace, string $name ) : void;


    public function reset() : bool;


    public function savePath( ?string $value = null ) : string|false;


    /** @param list<string> $namespace */
    public function set( array $namespace, string $name, mixed $value ) : void;


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