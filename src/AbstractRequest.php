<?php /** @noinspection PhpMethodNamingConventionInspection */


declare( strict_types = 1 );


namespace JDWX\Web;


use JDWX\Param\IParameter;
use JDWX\Param\IParameterSet;
use OutOfBoundsException;


abstract readonly class AbstractRequest implements RequestInterface {


    public function __construct( private IParameterSet   $setGet, private IParameterSet $setPost,
                                 private IParameterSet   $setCookie, private FilesHandler $files,
                                 private ServerInterface $server ) {}


    public function COOKIE( string $i_stName, mixed $i_xDefault = null ) : ?IParameter {
        return $this->setCookie->get( $i_stName, $i_xDefault );
    }


    public function FILES() : FilesHandler {
        return $this->files;
    }


    public function GET( string $i_stName, mixed $i_xDefault = null ) : ?IParameter {
        return $this->setGet->get( $i_stName, $i_xDefault );
    }


    public function POST( string $i_stName, mixed $i_xDefault = null ) : ?IParameter {
        return $this->setPost->get( $i_stName, $i_xDefault );
    }


    public function _COOKIE() : IParameterSet {
        return $this->setCookie;
    }


    public function _GET() : IParameterSet {
        return $this->setGet;
    }


    public function _POST() : IParameterSet {
        return $this->setPost;
    }


    public function cookieEx( string $i_stName, mixed $i_xDefault = null ) : IParameter {
        $np = $this->COOKIE( $i_stName, $i_xDefault );
        if ( $np instanceof IParameter ) {
            return $np;
        }
        throw new OutOfBoundsException( 'COOKIE parameter not found: ' . $i_stName );
    }


    /** @param string ...$i_rstNames */
    public function cookieHas( ...$i_rstNames ) : bool {
        return $this->setCookie->has( ...$i_rstNames );
    }


    public function getEx( string $i_stName, mixed $i_xDefault = null ) : IParameter {
        $np = $this->GET( $i_stName, $i_xDefault );
        if ( $np instanceof IParameter ) {
            return $np;
        }
        throw new OutOfBoundsException( 'GET parameter not found: ' . $i_stName );
    }


    /** @param string ...$i_rstNames */
    public function getHas( ...$i_rstNames ) : bool {
        return $this->setGet->has( ...$i_rstNames );
    }


    public function isGET() : bool {
        return 'GET' === $this->method();
    }


    public function isHEAD() : bool {
        return 'HEAD' === $this->method();
    }


    public function isPOST() : bool {
        return 'POST' === $this->method();
    }


    public function method() : string {
        return trim( $this->server->requestMethod() );
    }


    public function path() : string {
        return $this->uriParts()->path();
    }


    public function postEx( string $i_stName, mixed $i_xDefault = null ) : IParameter {
        $np = $this->POST( $i_stName, $i_xDefault );
        if ( $np instanceof IParameter ) {
            return $np;
        }
        throw new OutOfBoundsException( 'POST parameter not found: ' . $i_stName );
    }


    /** @param string ...$i_rstNames */
    public function postHas( ...$i_rstNames ) : bool {
        return $this->setPost->has( ...$i_rstNames );
    }


    public function server() : ServerInterface {
        return $this->server;
    }


    public function uri() : string {
        return $this->server->requestUri();
    }


    public function uriParts() : UrlParts {
        return Url::splitEx( $this->uri() );
    }


    /**
     * Make sure that the provided URI is valid and isn't up to any tricky
     * stuff.
     */
    public function validateUri() : bool {
        # Note that splitting the URL parts already checks the URI as a
        # whole for things like invalid characters.
        return $this->uriParts()->validate();
    }


}
