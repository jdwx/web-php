<?php


declare( strict_types = 1 );


namespace JDWX\Web;


interface ServerInterface {


    /** @param array<string, string>|null $i_nrDefaults */
    public function __construct( ?array $i_nrDefaults = null );


    public function documentRoot() : string;


    public function httpHost() : ?string;


    public function httpHostEx() : string;


    public function httpReferer() : ?string;


    public function httpRefererEx() : string;


    public function httpUserAgent() : ?string;


    public function httpUserAgentEx() : string;


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


    public function withDocumentRoot( string $i_stDocumentRoot ) : static;


    public function withHttpHost( ?string $i_nstHttpHost ) : static;


    public function withHttpReferer( ?string $i_nstHttpReferer ) : static;


    public function withHttpUserAgent( ?string $i_nstHttpUserAgent ) : static;


    public function withHttps( bool $i_bHttps ) : static;


    public function withPathInfo( string $i_stPathInfo ) : static;


    public function withPhpSelf( string $i_stPhpSelf ) : static;


    public function withRemoteAddr( string $i_stRemoteAddr ) : static;


    public function withRemotePort( int $i_uRemotePort ) : static;


    public function withRequestMethod( string $i_stRequestMethod ) : static;


    public function withRequestScheme( string $i_stRequestScheme ) : static;


    public function withRequestUri( string $i_stRequestUri ) : static;


    public function withScriptFilename( string $i_stScriptFilename ) : static;


    public function withScriptName( string $i_stScriptName ) : static;


    public function withServerAddr( string $i_stServerAddr ) : static;


    public function withServerName( string $i_stServerName ) : static;


}