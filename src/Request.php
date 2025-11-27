<?php


declare( strict_types = 1 );


namespace JDWX\Web;


use JDWX\Param\ParameterSet;
use JDWX\Web\Backends\FilesBackendInterface;
use LogicException;


/** This class encapsulates user input from a browser request (Cookies, GET query parameters,
 * POST values, and file uploads).  It is intended to be used as a singleton obtained via Request::get(),
 * possibly after initialization via Request::init() if you don't want the regular superglobals
 * used to populate the request.
 */
readonly class Request extends AbstractRequest {


    /**
     * @return RequestInterface
     *
     * This is used to retrieve a valid Request singleton, regardless of
     * whether it has been initialized already. As such, this is the most
     * common way to get a Request object in a live web request.
     */
    public static function getGlobal() : RequestInterface {
        return self::req() ?: static::init();
    }


    /**
     * @param array<string, string|list<string>>|null $i_GET
     * @param array<string, string|list<string>>|null $i_POST
     * @param array<string, string>|null $i_COOKIE
     * @param mixed[]|null $i_FILES
     *
     * This is used to initialize the Request singleton one time with specific values.
     * It is separated from getGlobal() for testing purposes.
     */
    public static function init( ?array           $i_GET = null, ?array $i_POST = null,
                                 ?array           $i_COOKIE = null, ?array $i_FILES = null,
                                 ?ServerInterface $i_server = null ) : RequestInterface {
        if ( self::req() instanceof self ) {
            throw new LogicException( 'Request already initialized.' );
        }
        return self::reqEx( static::synthetic( $i_GET, $i_POST, $i_COOKIE, $i_FILES, $i_server ) );
    }


    /**
     * @param array<string, string|list<string>>|null $i_GET
     * @param array<string, string|list<string>>|null $i_POST
     * @param array<string, string>|null $i_COOKIE
     * @param mixed[]|null $i_FILES
     *
     * This is public, so it can be used for testing and mocking objects. If you subclass
     * Request, you'll need to override this method to return an instance of your subclass.
     */
    public static function synthetic( ?array                 $i_GET = null, ?array $i_POST = null,
                                      ?array                 $i_COOKIE = null, ?array $i_FILES = null,
                                      ?ServerInterface       $i_server = null,
                                      ?FilesBackendInterface $i_filesBackend = null ) : self {
        return new self(
            new ParameterSet( $i_GET ?? $_GET ),
            new ParameterSet( $i_POST ?? $_POST ),
            new ParameterSet( $i_COOKIE ?? $_COOKIE ),
            new FilesHandler( $i_FILES ?? $_FILES, $i_filesBackend ),
            $i_server ?? new Server()
        );
    }


    protected static function req( ?RequestInterface $i_req = null, bool $i_bClear = false ) : ?RequestInterface {
        static $req = null;
        if ( $i_req instanceof RequestInterface ) {
            $req = $i_req;
        } elseif ( $i_bClear ) {
            $req = null;
        }
        return $req;
    }


    protected static function reqEx( RequestInterface $i_req ) : RequestInterface {
        $req = self::req( $i_req );
        assert( $req instanceof RequestInterface );
        return $req;
    }


}

