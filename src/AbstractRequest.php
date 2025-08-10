<?php /** @noinspection PhpMethodNamingConventionInspection */


declare( strict_types = 1 );


namespace JDWX\Web;


use JDWX\Json\Json;
use JDWX\Param\IParameter;
use JDWX\Param\IParameterSet;
use JDWX\Strict\OK;
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


    public function body() : ?string {
        return match ( $this->method() ) {
            'POST', 'PUT', 'PATCH' => $this->fetchInput(),
            default => null,
        };
    }


    public function bodyEx() : string {
        $nst = $this->body();
        if ( is_string( $nst ) ) {
            return $nst;
        }
        throw new OutOfBoundsException( 'Request body not available' );
    }


    public function bodyJson() : mixed {
        $nst = $this->body();
        if ( is_string( $nst ) ) {
            return Json::decode( $nst );
        }
        return null;
    }


    /** @return array<int|string, mixed>|null The decoded array or null if there isn't a body */
    public function bodyJsonArray() : ?array {
        $nst = $this->body();
        if ( ! is_string( $nst ) ) {
            return null;
        }
        return Json::decodeArray( $nst );
    }


    /** @return array<int|string, mixed> */
    public function bodyJsonArrayEx() : array {
        return Json::decodeArray( $this->bodyEx() );
    }


    public function bodyJsonEx() : mixed {
        $nst = $this->body();
        # This is implemented this way to allow for the JSON to return null (which is OK) and
        # distinguish that from the case where there isn't a body at all (which is an error).
        if ( ! empty( $nst ) ) {
            return Json::decode( $nst );
        }
        throw new OutOfBoundsException( 'Request body JSON not available' );
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


    public function parent() : string {
        return $this->uriParts()->parent()->__toString();
    }


    public function parentPath() : string {
        return $this->uriParts()->parent()->path();
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


    public function referer() : ?string {
        return $this->server->httpReferer();
    }


    public function refererEx() : string {
        $nst = $this->referer();
        if ( is_string( $nst ) ) {
            return $nst;
        }
        throw new OutOfBoundsException( 'Referer URL not available' );
    }


    public function refererParts() : ?UrlParts {
        $nst = $this->referer();
        if ( ! is_string( $nst ) ) {
            return null;
        }
        return Url::splitEx( $nst );
    }


    public function refererPartsEx() : UrlParts {
        $nur = $this->refererParts();
        if ( $nur instanceof UrlParts ) {
            return $nur;
        }
        throw new OutOfBoundsException( 'Referer URL parts not available' );
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


    /**
     * This is provided as a separate method so that it can be overridden for testing.
     * @codeCoverageIgnore
     */
    protected function fetchInput() : string {
        return OK::file_get_contents( 'php://input' );
    }


}
