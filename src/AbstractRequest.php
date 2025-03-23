<?php /** @noinspection PhpMethodNamingConventionInspection */


declare( strict_types = 1 );


namespace JDWX\Web;


use JDWX\Param\IParameter;
use JDWX\Param\IParameterSet;
use JDWX\Param\ParameterSet;
use OutOfBoundsException;


abstract readonly class AbstractRequest implements RequestInterface {


    public function __construct( private ParameterSet    $setGet, private ParameterSet $setPost,
                                 private ParameterSet    $setCookie, private FilesHandler $files,
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


    public function cookieEx( string $i_stName ) : IParameter {
        $np = $this->COOKIE( $i_stName );
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
        return 'get' === $this->method();
    }


    public function isPOST() : bool {
        return 'post' === $this->method();
    }


    public function method() : string {
        return strtolower( trim( $this->server->requestMethod() ) );
    }


    public function path() : string {
        return $this->uriParts()->path();
    }


    public function postEx( string $i_stName, mixed $i_xDefault = null ) : IParameter {
        $np = $this->POST( $i_stName );
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


}
