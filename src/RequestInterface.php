<?php /** @noinspection PhpMethodNamingConventionInspection */


declare( strict_types = 1 );


namespace JDWX\Web;


use JDWX\Param\IParameter;
use JDWX\Param\IParameterSet;
use OutOfBoundsException;


interface RequestInterface {


    public function COOKIE( string $i_stName, mixed $i_xDefault = null ) : ?IParameter;


    public function FILES() : FilesHandler;


    public function GET( string $i_stName, mixed $i_xDefault = null ) : ?IParameter;


    public function POST( string $i_stName, mixed $i_xDefault = null ) : ?IParameter;


    public function _COOKIE() : IParameterSet;


    public function _GET() : IParameterSet;


    public function _POST() : IParameterSet;


    public function body() : ?string;


    /**
     * @return string The body of the request.
     * @throws OutOfBoundsException If there isn't a request body.
     */
    public function bodyEx() : string;


    /**
     * @return mixed The decoded JSON or null if there isn't a body
     * @throws \JsonException
     */
    public function bodyJson() : mixed;


    /**
     * @return array<int|string, mixed>|null The decoded array or null if there isn't a body
     * @throws \JsonException
     */
    public function bodyJsonArray() : ?array;


    /**
     * @return array<int|string, mixed>
     * @throws \JsonException If the body does not decode as a JSON array (list or dictionary).
     * @throws OutOfBoundsException If there is no request body
     */
    public function bodyJsonArrayEx() : array;


    /**
     * @return mixed The decoded JSON
     * @throws \JsonException If the body does not decode as JSON.
     * @throws OutOfBoundsException If there is no request body
     */
    public function bodyJsonEx() : mixed;


    /**
     * @return string|null The value of the request's content-type header, if present, otherwise null.
     */
    public function contentType() : ?string;


    public function cookieEx( string $i_stName, mixed $i_xDefault = null ) : IParameter;


    /** @param string ...$i_rstNames */
    public function cookieHas( string ...$i_rstNames ) : bool;


    public function getEx( string $i_stName, mixed $i_xDefault = null ) : IParameter;


    /** @param string ...$i_rstNames */
    public function getHas( ...$i_rstNames ) : bool;


    public function isGET() : bool;


    public function isHEAD() : bool;


    public function isPOST() : bool;


    public function method() : string;


    public function parent() : string;


    public function parentPath() : string;


    public function path() : string;


    public function postEx( string $i_stName, mixed $i_xDefault = null ) : IParameter;


    /** @param string ...$i_rstNames */
    public function postHas( ...$i_rstNames ) : bool;


    public function referer() : ?string;


    public function refererEx() : string;


    public function refererParts() : ?UrlParts;


    public function refererPartsEx() : UrlParts;


    public function server() : ServerInterface;


    public function uri() : string;


    public function uriParts() : UrlParts;


    public function validateUri() : bool;


}