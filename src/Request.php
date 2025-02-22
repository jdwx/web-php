<?php


declare( strict_types = 1 );


namespace JDWX\Web;


use JDWX\Param\ParameterSet;
use JDWX\Web\Backends\FilesBackendInterface;
use LogicException;


/** This class encapsulates user input from a browser request (Cookies, GET query parameters
 * POST values, and file uploads).  It is intended to be used as a singleton obtained via Request::get()
 * possibly after initialization via Request::init() if you don't want the regular superglobals
 * used to populate the request.
 */
class Request extends AbstractRequest {


    protected static ?self $req = null;


    /**
     * @param array<string, string|list<string>>|null $i_GET
     * @param array<string, string|list<string>>|null $i_POST
     * @param array<string, string>|null $i_COOKIE
     * @param mixed[]|null $i_FILES
     */
    protected function __construct( ?array                 $i_GET = null, ?array $i_POST = null,
                                    ?array                 $i_COOKIE = null, ?array $i_FILES = null,
                                    ?string                $i_nstMethod = null,
                                    ?string                $i_nstUri = null,
                                    ?FilesBackendInterface $i_filesBackend = null ) {
        $this->setGet = new ParameterSet( $i_GET ?? $_GET );
        $this->setPost = new ParameterSet( $i_POST ?? $_POST );
        $this->setCookie = new ParameterSet( $i_COOKIE ?? $_COOKIE );
        $this->files = new FilesHandler( $i_FILES ?? $_FILES, $i_filesBackend );
        $this->stMethod = $i_nstMethod ?? Server::requestMethod();
        $this->stUri = $i_nstUri ?? Server::requestUri();
    }


    /**
     * @return Request
     *
     * This is used to retrieve a valid Request singleton, regardless of
     * whether it has been initialized already. As such, this is the most
     * common way to get a Request object in a live web request.
     */
    public static function getGlobal() : self {
        $x = static::$req;
        if ( ! $x instanceof Request ) {
            return static::init();
        }
        return $x;
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
    public static function init( ?array  $i_GET = null, ?array $i_POST = null,
                                 ?array  $i_COOKIE = null, ?array $i_FILES = null,
                                 ?string $i_nstMethod = null,
                                 ?string $i_nstUri = null ) : self {
        if ( static::$req instanceof self ) {
            throw new LogicException( 'Request already initialized.' );
        }
        static::$req = static::synthetic( $i_GET, $i_POST, $i_COOKIE, $i_FILES, $i_nstMethod, $i_nstUri );
        return static::$req;
    }


    /**
     * @param array<string, string|list<string>>|null $i_GET
     * @param array<string, string|list<string>>|null $i_POST
     * @param array<string, string>|null $i_COOKIE
     * @param mixed[]|null $i_FILES
     *
     * This is public so it can be used for testing and mocking objects. If you subclass
     * Request, you'll need to override this method to return an instance of your subclass.
     */
    public static function synthetic( ?array                 $i_GET = null, ?array $i_POST = null,
                                      ?array                 $i_COOKIE = null, ?array $i_FILES = null,
                                      ?string                $i_nstMethod = null,
                                      ?string                $i_nstUri = null,
                                      ?FilesBackendInterface $i_filesBackend = null ) : self {
        return new self( $i_GET, $i_POST, $i_COOKIE, $i_FILES, $i_nstMethod, $i_nstUri, $i_filesBackend );
    }


}

