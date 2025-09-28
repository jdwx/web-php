<?php


declare( strict_types = 1 );


namespace JDWX\Web\Framework;


use JDWX\Strict\OK;
use JDWX\Strict\TypeIs;
use JDWX\Web\Framework\Exceptions\HttpStatusException;
use JDWX\Web\Pages\SimpleHtmlPage;


class HttpError {


    use HttpTrait;


    /** @var array<int, string> */
    public const array ERROR_NAMES = [
        400 => 'Bad Request',
        401 => 'Authorization Required',
        403 => 'Access Denied',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        410 => 'Gone',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        503 => 'Service Unavailable',
    ];


    /** @var array<int, string> */
    public const array ERROR_TEXT = [
        401 => 'You must be logged in to access this resource.',
        403 => 'The file you requested could not be accessed.',
        404 => 'The file or resource you requested was not found.',
        405 => 'The method you used to access this resource is not allowed.',
        406 => 'The resource you requested is not available in a format you accept.',
        410 => 'The resource you requested is no longer available.',
        500 => 'An internal error occurred while processing your request.',
        501 => 'The server does not support the functionality required to fulfill your request.',
        503 => 'A temporary error occurred while processing your request.',
    ];


    public function __construct( protected ?string $nstErrorPath = null ) {}


    public function errorName( int $i_uHTTPStatus, ?string $i_nstErrorName = null ) : string {
        return $i_nstErrorName ?? self::ERROR_NAMES[ $i_uHTTPStatus ] ?? 'Unknown Error';
    }


    public function errorText( int $i_uHTTPStatus, ?string $i_nstErrorText = null ) : string {
        return $i_nstErrorText ?? self::ERROR_TEXT[ $i_uHTTPStatus ] ?? '';
    }


    public function render( int     $i_uHTTPStatus, ?string $i_nstErrorName = null,
                            ?string $i_nstErrorText = null ) : string {
        # Set the HTTP status
        $this->setResponseCode( $i_uHTTPStatus );

        # If there is an error page for this error, use it.
        $path = $this->errorPath( $i_uHTTPStatus );
        if ( is_string( $path ) ) {
            OK::ob_start();
            require $path;
            return OK::ob_get_clean();
        }

        $stErrorName = $this->errorName( $i_uHTTPStatus, $i_nstErrorName );
        $stErrorText = $this->errorText( $i_uHTTPStatus, $i_nstErrorText );

        # As a last-ditch effort, build a simple error page.
        $this->setHeader( 'Content-Type: text/html' );
        $page = new SimpleHtmlPage();
        $page->setTitle( "{$i_uHTTPStatus} {$stErrorName}" );
        $page->addContent( "<h1>{$i_uHTTPStatus} {$stErrorName}</h1>" );
        $page->addContent( "<p>{$stErrorText}</p>" );
        return $page->render();
    }


    public function show( int     $i_uHTTPStatus, ?string $i_nstErrorName = null,
                          ?string $i_nstErrorText = null ) : void {
        echo $this->render( $i_uHTTPStatus, $i_nstErrorName, $i_nstErrorText );
    }


    public function showException( HttpStatusException $i_e ) : void {
        $this->show( TypeIs::int( $i_e->getCode() ), $i_e->display() );
    }


    protected function errorPath( int $i_uHTTPStatus ) : ?string {
        if ( ! is_string( $this->nstErrorPath ) ) {
            return null;
        }
        $stPath = str_replace( '%d', (string) $i_uHTTPStatus, $this->nstErrorPath );
        if ( file_exists( $stPath ) ) {
            return $stPath;
        }
        return null;
    }


}
