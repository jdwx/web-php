<?php /** @noinspection PhpUnused */


declare( strict_types = 1 );


namespace JDWX\Web;


/** This class encapsulates the $_SERVER superglobal to allow type & error checking. */
class Server implements ServerInterface {


    protected const array DEFAULTS = [
        'DOCUMENT_ROOT' => '',
        'HTTP_HOST' => '',
        'HTTP_REFERER' => '',
        'HTTP_USER_AGENT' => '',
        'HTTPS' => false,
        'PATH_INFO' => '',
        'PHP_SELF' => '',
        'REMOTE_ADDR' => '',
        'REMOTE_PORT' => 0,
        'REQUEST_METHOD' => '',
        'REQUEST_SCHEME' => '',
        'REQUEST_URI' => '',
        'SCRIPT_FILENAME' => '',
        'SCRIPT_NAME' => '',
        'SERVER_ADDR' => '',
        'SERVER_NAME' => '',
    ];


    private string $stDocumentRoot;

    private string $stHttpHost;

    private string $stHttpReferer;

    private string $stHttpUserAgent;

    private bool $bHttps;

    private string $stPathInfo;

    private string $stPhpSelf;

    private string $stRemoteAddr;

    private int $iRemotePort;

    private string $stRequestScheme;

    private string $stRequestMethod;

    private string $stRequestUri;

    private string $stScriptFilename;

    private string $stScriptName;

    private string $stServerAddr;

    private string $stServerName;


    /** @param array<string, string>|null $i_nrDefaults */
    public function __construct( ?array $i_nrDefaults = null ) {
        $i_nrDefaults ??= $_SERVER;
        $this->stDocumentRoot = $i_nrDefaults[ 'DOCUMENT_ROOT' ] ?? static::DEFAULTS[ 'DOCUMENT_ROOT' ];
        $this->stHttpHost = $i_nrDefaults[ 'HTTP_HOST' ] ?? static::DEFAULTS[ 'HTTP_HOST' ];
        $this->stHttpReferer = $i_nrDefaults[ 'HTTP_REFERER' ] ?? static::DEFAULTS[ 'HTTP_REFERER' ];
        $this->stHttpUserAgent = $i_nrDefaults[ 'HTTP_USER_AGENT' ] ?? static::DEFAULTS[ 'HTTP_USER_AGENT' ];
        $this->bHttps = isset( $i_nrDefaults[ 'HTTPS' ] )
            ? ( $i_nrDefaults[ 'HTTPS' ] === 'on' )
            : static::DEFAULTS[ 'HTTPS' ];
        $this->stPathInfo = $i_nrDefaults[ 'PATH_INFO' ] ?? static::DEFAULTS[ 'PATH_INFO' ];
        $this->stPhpSelf = $i_nrDefaults[ 'PHP_SELF' ] ?? static::DEFAULTS[ 'PHP_SELF' ];
        $this->stRemoteAddr = $i_nrDefaults[ 'REMOTE_ADDR' ] ?? static::DEFAULTS[ 'REMOTE_ADDR' ];
        $this->iRemotePort = isset( $i_nrDefaults[ 'REMOTE_PORT' ] )
            ? intval( $i_nrDefaults[ 'REMOTE_PORT' ] )
            : static::DEFAULTS[ 'REMOTE_PORT' ];
        $this->stRequestMethod = $i_nrDefaults[ 'REQUEST_METHOD' ] ?? static::DEFAULTS[ 'REQUEST_METHOD' ];
        $this->stRequestScheme = $i_nrDefaults[ 'REQUEST_SCHEME' ] ?? static::DEFAULTS[ 'REQUEST_SCHEME' ];
        $this->stRequestUri = $i_nrDefaults[ 'REQUEST_URI' ] ?? static::DEFAULTS[ 'REQUEST_URI' ];
        $this->stScriptFilename = $i_nrDefaults[ 'SCRIPT_FILENAME' ] ?? static::DEFAULTS[ 'SCRIPT_FILENAME' ];
        $this->stScriptName = $i_nrDefaults[ 'SCRIPT_NAME' ] ?? static::DEFAULTS[ 'SCRIPT_NAME' ];
        $this->stServerAddr = $i_nrDefaults[ 'SERVER_ADDR' ] ?? static::DEFAULTS[ 'SERVER_ADDR' ];
        $this->stServerName = $i_nrDefaults[ 'SERVER_NAME' ] ?? static::DEFAULTS[ 'SERVER_NAME' ];
    }


    public function documentRoot() : string {
        return $this->stDocumentRoot;
    }


    public function httpHost() : string {
        return $this->stHttpHost;
    }


    public function httpReferer() : string {
        return $this->stHttpReferer;
    }


    public function httpUserAgent() : string {
        return $this->stHttpUserAgent;
    }


    public function https() : bool {
        return $this->bHttps;
    }


    public function pathInfo() : string {
        return $this->stPathInfo;
    }


    public function phpSelf() : string {
        return $this->stPhpSelf;
    }


    public function remoteAddr() : string {
        return $this->stRemoteAddr;
    }


    public function remotePort() : int {
        return $this->iRemotePort;
    }


    public function requestMethod() : string {
        return $this->stRequestMethod;
    }


    public function requestScheme() : string {
        return $this->stRequestScheme;
    }


    public function requestUri() : string {
        return $this->stRequestUri;
    }


    public function scriptFilename() : string {
        return $this->stScriptFilename;
    }


    public function scriptName() : string {
        return $this->stScriptName;
    }


    public function serverAddr() : string {
        return $this->stServerAddr;
    }


    public function serverName() : string {
        return $this->stServerName;
    }


    public function withDocumentRoot( string $i_stDocumentRoot ) : self {
        $out = clone $this;
        $out->stDocumentRoot = $i_stDocumentRoot;
        return $out;
    }


    public function withHttpHost( string $i_stHttpHost ) : self {
        $out = clone $this;
        $out->stHttpHost = $i_stHttpHost;
        return $out;
    }


    public function withHttpReferer( string $i_stHttpReferer ) : self {
        $out = clone $this;
        $out->stHttpReferer = $i_stHttpReferer;
        return $out;
    }


    public function withHttpUserAgent( string $i_stHttpUserAgent ) : self {
        $out = clone $this;
        $out->stHttpUserAgent = $i_stHttpUserAgent;
        return $out;
    }


    public function withHttps( bool $i_bHttps ) : self {
        $out = clone $this;
        $out->bHttps = $i_bHttps;
        return $out;
    }


    public function withPathInfo( string $i_stPathInfo ) : self {
        $out = clone $this;
        $out->stPathInfo = $i_stPathInfo;
        return $out;
    }


    public function withPhpSelf( string $i_stPhpSelf ) : self {
        $out = clone $this;
        $out->stPhpSelf = $i_stPhpSelf;
        return $out;
    }


    public function withRemoteAddr( string $i_stRemoteAddr ) : self {
        $out = clone $this;
        $out->stRemoteAddr = $i_stRemoteAddr;
        return $out;
    }


    public function withRemotePort( int $i_iRemotePort ) : self {
        $out = clone $this;
        $out->iRemotePort = $i_iRemotePort;
        return $out;
    }


    public function withRequestMethod( string $i_stRequestMethod ) : self {
        $out = clone $this;
        $out->stRequestMethod = $i_stRequestMethod;
        return $out;
    }


    public function withRequestScheme( string $i_stRequestScheme ) : self {
        $out = clone $this;
        $out->stRequestScheme = $i_stRequestScheme;
        return $out;
    }


    public function withRequestUri( string $i_stRequestUri ) : self {
        $out = clone $this;
        $out->stRequestUri = $i_stRequestUri;
        return $out;
    }


    public function withScriptFilename( string $i_stScriptFilename ) : self {
        $out = clone $this;
        $out->stScriptFilename = $i_stScriptFilename;
        return $out;
    }


    public function withScriptName( string $i_stScriptName ) : self {
        $out = clone $this;
        $out->stScriptName = $i_stScriptName;
        return $out;
    }


    public function withServerAddr( string $i_stServerAddr ) : self {
        $out = clone $this;
        $out->stServerAddr = $i_stServerAddr;
        return $out;
    }


    public function withServerName( string $i_stServerName ) : self {
        $out = clone $this;
        $out->stServerName = $i_stServerName;
        return $out;
    }


}
