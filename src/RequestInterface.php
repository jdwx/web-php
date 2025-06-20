<?php /** @noinspection PhpMethodNamingConventionInspection */


declare( strict_types = 1 );


namespace JDWX\Web;


use JDWX\Param\IParameter;
use JDWX\Param\IParameterSet;


interface RequestInterface {


    public function COOKIE( string $i_stName, mixed $i_xDefault = null ) : ?IParameter;


    public function FILES() : FilesHandler;


    public function GET( string $i_stName, mixed $i_xDefault = null ) : ?IParameter;


    public function POST( string $i_stName, mixed $i_xDefault = null ) : ?IParameter;


    public function _COOKIE() : IParameterSet;


    public function _GET() : IParameterSet;


    public function _POST() : IParameterSet;


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