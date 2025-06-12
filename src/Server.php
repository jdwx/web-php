<?php /** @noinspection PhpUnused */


declare( strict_types = 1 );


namespace JDWX\Web;


use JDWX\Strict\TypeIs;


/** This class encapsulates the $_SERVER superglobal to allow type and error checking. */
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

    /** @var array<string, ?string> */
    private array $rFromHttpHeaders;


    /** @param array<string, ?string>|null $i_nrDefaults */
    public function __construct( ?array $i_nrDefaults = null ) {
        $i_nrDefaults ??= $_SERVER;
        $this->stDocumentRoot = $i_nrDefaults[ 'DOCUMENT_ROOT' ] ?? static::DEFAULTS[ 'DOCUMENT_ROOT' ];
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

        $rFromHttpHeaders = array_filter( array_merge( static::DEFAULTS, $i_nrDefaults ), function ( $key ) {
            return str_starts_with( $key, 'HTTP_' );
        }, ARRAY_FILTER_USE_KEY );
        $this->rFromHttpHeaders = TypeIs::mapNullableString( $rFromHttpHeaders );

    }


    public function documentRoot() : string {
        return $this->stDocumentRoot;
    }


    public function httpHeader( string $i_stKey, ?string $i_nstDefault = null ) : ?string {
        return $this->rFromHttpHeaders[ $i_stKey ] ?? $i_nstDefault;
    }


    public function httpHeaderEx( string $i_stKey, ?string $i_nstDefault = null ) : string {
        $nst = $this->httpHeader( $i_stKey, $i_nstDefault );
        if ( is_string( $nst ) ) {
            return $nst;
        }
        throw new \RuntimeException( "{$i_stKey} is required but not set" );
    }


    public function httpHost( ?string $i_nstDefault = null ) : ?string {
        return $this->httpHeader( 'HTTP_HOST', $i_nstDefault );
    }


    public function httpHostEx( ?string $i_nstDefault = null ) : string {
        return $this->httpHeaderEx( 'HTTP_HOST', $i_nstDefault );
    }


    public function httpReferer( ?string $i_nstDefault = null ) : ?string {
        return $this->httpHeader( 'HTTP_REFERER', $i_nstDefault );
    }


    public function httpRefererEx( ?string $i_nstDefault = null ) : string {
        return $this->httpHeaderEx( 'HTTP_REFERER', $i_nstDefault );
    }


    public function httpUserAgent( ?string $i_nstDefault = null ) : ?string {
        return $this->httpHeader( 'HTTP_USER_AGENT', $i_nstDefault );
    }


    public function httpUserAgentEx( ?string $i_nstDefault = null ) : string {
        return $this->httpHeaderEx( 'HTTP_USER_AGENT', $i_nstDefault );
    }


    public function https() : bool {
        return $this->bHttps;
    }


    public function isRequestMethod( string $i_stMethod ) : bool {
        return strtolower( trim( $i_stMethod ) ) === strtolower( trim( $this->requestMethod() ) );
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


    public function withHttpHeader( string $i_stKey, ?string $i_nstValue ) : static {
        $r = $this->_export();
        if ( is_null( $i_nstValue ) ) {
            unset( $r[ $i_stKey ] );
        } else {
            $r[ $i_stKey ] = $i_nstValue;
        }
        return new static( $r );
    }


    public function withHttpHost( ?string $i_nstHttpHost ) : static {
        return $this->withHttpHeader( 'HTTP_HOST', $i_nstHttpHost );
    }


    public function withHttpReferer( ?string $i_nstHttpReferer ) : static {
        return $this->withHttpHeader( 'HTTP_REFERER', $i_nstHttpReferer );
    }


    public function withHttpUserAgent( ?string $i_nstHttpUserAgent ) : static {
        return $this->withHttpHeader( 'HTTP_USER_AGENT', $i_nstHttpUserAgent );
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
        return $this->rFromHttpHeaders + [
                'DOCUMENT_ROOT' => $this->stDocumentRoot,
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
