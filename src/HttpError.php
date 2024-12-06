<?php


declare( strict_types = 1 );


namespace JDWX\Web;


class HttpError {


    /** @var array<int, string> */
    protected const ERROR_NAMES = [
        400 => 'Bad Request',
        401 => 'Authorization Required',
        403 => 'AccessDenied',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        410 => 'Gone',
        500 => 'Internal Server Error',
        503 => 'Service Unavailable',
    ];


    /** @var array<int, string> */
    protected const ERROR_TEXT = [
        401 => 'You must be logged in to access this resource.',
        403 => 'The file you requested could not be accessed.',
        404 => 'The file or resource you requested was not found.',
        405 => 'The method you used to access this resource is not allowed.',
        406 => 'The resource you requested is not available in a format you accept.',
        410 => 'The resource you requested is no longer available.',
        500 => 'An internal error occurred while processing your request.',
        503 => 'A temporary error occurred while processing your request.',
    ];


    public function errorName( int $i_uHTTPStatus, ?string $i_nstErrorName = null ) : string {
        if ( is_string( $i_nstErrorName ) ) {
            return $i_nstErrorName;
        }
        return self::ERROR_NAMES[ $i_uHTTPStatus ] ?? 'Unknown Error';
    }


    public function errorText( int $i_uHTTPStatus, ?string $i_nstErrorName = null ) : string {
        if ( is_string( $i_nstErrorName ) ) {
            return $i_nstErrorName;
        }
        return self::ERROR_TEXT[ $i_uHTTPStatus ] ?? '';
    }


    public function show( int     $i_uHTTPStatus, ?string $i_nstErrorName = null,
                          ?string $i_nstErrorText = null ) : void {

        # Set the HTTP status
        http_response_code( $i_uHTTPStatus );

        # If there is an error page for this error, use it.
        $path = $this->errorPath( $i_uHTTPStatus );
        if ( is_string( $path ) && file_exists( $path ) ) {
            require $path;
            return;
        }

        $stErrorName = $this->errorName( $i_uHTTPStatus, $i_nstErrorName );
        $stErrorText = $this->errorText( $i_uHTTPStatus, $i_nstErrorText );

        # As a last-ditch effort, build a simple error page.
        header( 'Content-Type: text/html' );
        echo '<!DOCTYPE html>';
        echo "<html lang=\"en_US\">";
        echo "<head><title>{$i_uHTTPStatus} {$stErrorName}</title></head>";
        echo "<body><h1>{$i_uHTTPStatus} {$stErrorName}</h1><p>{$stErrorText}</p></body></html>";
    }


    /** @noinspection PhpUnusedParameterInspection */


    protected function errorPath( int $i_uHTTPStatus ) : ?string {
        return null;
    }


}
