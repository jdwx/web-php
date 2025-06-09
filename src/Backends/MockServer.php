<?php


declare( strict_types = 1 );


namespace JDWX\Web\Backends;


use JDWX\Web\Server;


readonly class MockServer extends Server {


    protected const array EXTRA_DEFAULTS = [
        'DOCUMENT_ROOT' => '/var/www/html',
        'HTTP_HOST' => 'www.example.com',
        'HTTP_REFERER' => 'https://www.example.org/referer.html',
        'HTTP_USER_AGENT' => 'Super Fake User Agent',
        'HTTPS' => true,
        'PATH_INFO' => '/path/to/script.php',
        'PHP_SELF' => '/index.php',
        'REMOTE_ADDR' => '127.0.0.1',
        'REMOTE_PORT' => 12345,
        'REQUEST_METHOD' => 'GET',
        'REQUEST_SCHEME' => 'https',
        'REQUEST_URI' => '/',
        'SCRIPT_FILENAME' => '/var/www/html/index.php',
        'SCRIPT_NAME' => '/index.php',
        'SERVER_ADDR' => '192.0.2.1',
        'SERVER_NAME' => 'www.example.com',
    ];


    /** @param array<string, bool|int|string> $i_rDefaults */
    public static function POST( array $i_rDefaults = [] ) : self {
        $i_rDefaults[ 'REQUEST_METHOD' ] = 'POST';
        return self::new( $i_rDefaults );
    }


    public static function new( array $i_rDefaults = [] ) : self {
        $i_rDefaults = array_merge( self::EXTRA_DEFAULTS, $i_rDefaults );
        return new self( $i_rDefaults );
    }


}
