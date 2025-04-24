<?php


declare( strict_types = 1 );


namespace JDWX\Web;


interface ServerInterface {


    /** @param array<string, string>|null $i_nrDefaults */
    public function __construct( ?array $i_nrDefaults = null );


    public function documentRoot() : string;


    public function httpHost() : string;


    public function httpReferer() : string;


    public function httpUserAgent() : string;


    public function https() : bool;


    public function pathInfo() : string;


    public function phpSelf() : string;


    public function remoteAddr() : string;


    public function remotePort() : int;


    public function requestMethod() : string;


    public function requestScheme() : string;


    public function requestUri() : string;


    public function scriptFilename() : string;


    public function scriptName() : string;


    public function serverAddr() : string;


    public function serverName() : string;


}