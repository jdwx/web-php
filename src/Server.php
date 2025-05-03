<?php /** @noinspection PhpUnused */


declare( strict_types = 1 );


namespace JDWX\Web;


/** This class encapsulates the $_SERVER superglobal to allow type & error checking. */
readonly class Server implements ServerInterface {


    protected const array DEFAULTS = [
        'DOCUMENT_ROOT' => '',
        'HTTP_HOST' => null,
        'HTTP_REFERER' => null,
        'HTTP_USER_AGENT' => null,
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

    private ?string $nstHttpHost;

    private ?string $nstHttpReferer;

    private ?string $nstHttpUserAgent;

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


    /** @param array<string, ?string>|null $i_nrDefaults */
    public function __construct( ?array $i_nrDefaults = null ) {
        $i_nrDefaults ??= $_SERVER;
        $this->stDocumentRoot = $i_nrDefaults[ 'DOCUMENT_ROOT' ] ?? static::DEFAULTS[ 'DOCUMENT_ROOT' ];
        $this->nstHttpHost = $i_nrDefaults[ 'HTTP_HOST' ] ?? static::DEFAULTS[ 'HTTP_HOST' ];
        $this->nstHttpReferer = $i_nrDefaults[ 'HTTP_REFERER' ] ?? static::DEFAULTS[ 'HTTP_REFERER' ];
        $this->nstHttpUserAgent = $i_nrDefaults[ 'HTTP_USER_AGENT' ] ?? static::DEFAULTS[ 'HTTP_USER_AGENT' ];
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


    public function httpHost() : ?string {
        return $this->nstHttpHost;
    }


    public function httpHostEx() : string {
        $nst = $this->httpHost();
        if ( is_string( $nst ) ) {
            return $nst;
        }
        throw new \RuntimeException( 'HTTP_HOST is required but not set' );
    }


    public function httpReferer() : ?string {
        return $this->nstHttpReferer;
    }


    public function httpRefererEx() : string {
        $nst = $this->httpReferer();
        if ( is_string( $nst ) ) {
            return $nst;
        }
        throw new \RuntimeException( 'HTTP_REFERER is required but not set' );
    }


    public function httpUserAgent() : ?string {
        return $this->nstHttpUserAgent;
    }


    public function httpUserAgentEx() : string {
        $nst = $this->httpUserAgent();
        if ( is_string( $nst ) ) {
            return $nst;
        }
        throw new \RuntimeException( 'HTTP_USER_AGENT is required but not set' );
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


    public function withDocumentRoot( string $i_stDocumentRoot ) : static {
        $r = $this->_export();
        $r[ 'DOCUMENT_ROOT' ] = $i_stDocumentRoot;
        return new static( $r );
    }


    public function withHttpHost( ?string $i_nstHttpHost ) : static {
        $r = $this->_export();
        $r[ 'HTTP_HOST' ] = $i_nstHttpHost;
        return new static( $r );
    }


    public function withHttpReferer( ?string $i_nstHttpReferer ) : static {
        $r = $this->_export();
        $r[ 'HTTP_REFERER' ] = $i_nstHttpReferer;
        return new static( $r );
    }


    public function withHttpUserAgent( ?string $i_nstHttpUserAgent ) : static {
        $r = $this->_export();
        $r[ 'HTTP_USER_AGENT' ] = $i_nstHttpUserAgent;
        return new static( $r );
    }


    public function withHttps( bool $i_bHttps ) : static {
        $r = $this->_export();
        $r[ 'HTTPS' ] = $i_bHttps ? 'on' : 'off';
        return new static( $r );
    }


    public function withPathInfo( string $i_stPathInfo ) : static {
        $r = $this->_export();
        $r[ 'PATH_INFO' ] = $i_stPathInfo;
        return new static( $r );
    }


    public function withPhpSelf( string $i_stPhpSelf ) : static {
        $r = $this->_export();
        $r[ 'PHP_SELF' ] = $i_stPhpSelf;
        return new static( $r );
    }


    public function withRemoteAddr( string $i_stRemoteAddr ) : static {
        $r = $this->_export();
        $r[ 'REMOTE_ADDR' ] = $i_stRemoteAddr;
        return new static( $r );
    }


    public function withRemotePort( int $i_uRemotePort ) : static {
        $r = $this->_export();
        $r[ 'REMOTE_PORT' ] = strval( $i_uRemotePort );
        return new static( $r );
    }


    public function withRequestMethod( string $i_stRequestMethod ) : static {
        $r = $this->_export();
        $r[ 'REQUEST_METHOD' ] = $i_stRequestMethod;
        return new static( $r );
    }


    public function withRequestScheme( string $i_stRequestScheme ) : static {
        $r = $this->_export();
        $r[ 'REQUEST_SCHEME' ] = $i_stRequestScheme;
        return new static( $r );
    }


    public function withRequestUri( string $i_stRequestUri ) : static {
        $r = $this->_export();
        $r[ 'REQUEST_URI' ] = $i_stRequestUri;
        return new static( $r );
    }


    public function withScriptFilename( string $i_stScriptFilename ) : static {
        $r = $this->_export();
        $r[ 'SCRIPT_FILENAME' ] = $i_stScriptFilename;
        return new static( $r );
    }


    public function withScriptName( string $i_stScriptName ) : static {
        $r = $this->_export();
        $r[ 'SCRIPT_NAME' ] = $i_stScriptName;
        return new static( $r );
    }


    public function withServerAddr( string $i_stServerAddr ) : static {
        $r = $this->_export();
        $r[ 'SERVER_ADDR' ] = $i_stServerAddr;
        return new static( $r );
    }


    public function withServerName( string $i_stServerName ) : static {
        $r = $this->_export();
        $r[ 'SERVER_NAME' ] = $i_stServerName;
        return new static( $r );
    }


    /** @return array<string, ?string> */
    private function _export() : array {
        return [
            'DOCUMENT_ROOT' => $this->stDocumentRoot,
            'HTTP_HOST' => $this->nstHttpHost,
            'HTTP_REFERER' => $this->nstHttpReferer,
            'HTTP_USER_AGENT' => $this->nstHttpUserAgent,
            'HTTPS' => $this->bHttps ? 'on' : 'off',
            'PATH_INFO' => $this->stPathInfo,
            'PHP_SELF' => $this->stPhpSelf,
            'REMOTE_ADDR' => $this->stRemoteAddr,
            'REMOTE_PORT' => strval( $this->iRemotePort ),
            'REQUEST_METHOD' => $this->stRequestMethod,
            'REQUEST_SCHEME' => $this->stRequestScheme,
            'REQUEST_URI' => $this->stRequestUri,
            'SCRIPT_FILENAME' => $this->stScriptFilename,
            'SCRIPT_NAME' => $this->stScriptName,
            'SERVER_ADDR' => $this->stServerAddr,
            'SERVER_NAME' => $this->stServerName,
        ];
    }


}
