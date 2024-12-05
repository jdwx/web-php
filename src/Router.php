<?php /** @noinspection PhpUnused */


declare( strict_types = 1 );


namespace JDWX\Web;


use Psr\Log\LoggerInterface;


abstract class Router implements IRouter {


    /** @var array<int, string> */
    protected array $rErrorNames = [
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
    protected array $rErrorText = [
        401 => 'You must be logged in to access this resource.',
        403 => 'The file you requested could not be accessed.',
        404 => 'The file or resource you requested was not found.',
        405 => 'The method you used to access this resource is not allowed.',
        406 => 'The resource you requested is not available in a format you accept.',
        410 => 'The resource you requested is no longer available.',
        500 => 'An internal error occurred while processing your request. Our technical support has been automatically notified of the problem.',
        503 => 'A temporary error occurred while processing your request. Our technical support has been automatically notified of the problem.',
    ];


    final public function __construct( protected ?LoggerInterface $logger = null ) {}


    /** @param mixed[] $i_rContext */
    protected function abort( string $i_stError, array $i_rContext = [] ) : never {
        $this->logger?->warning( $i_stError, $i_rContext );
        exit( 10 );
    }


    protected function error( int $i_uHTTPStatus, ?string $i_nstErrorName = null, ?string $i_nstErrorText = null ) : void {

        if ( null === $i_nstErrorName ) {
            $i_nstErrorName = $this->rErrorNames[ $i_uHTTPStatus ] ?? 'Unknown Error';
        }
        if ( null === $i_nstErrorText ) {
            $i_nstErrorText = $this->rErrorText[ $i_uHTTPStatus ] ?? '';
        }

        # Set the HTTP status
        http_response_code( $i_uHTTPStatus );

        # If there is an error page for this error, use it.
        $path = $this->errorPath( $i_uHTTPStatus );
        if ( is_string( $path ) && file_exists( $path ) ) {
            require $path;
            return;
        }

        # As a last-ditch effort, build a simple error page.
        header( 'Content-Type: text/html' );
        echo '<!DOCTYPE html>';
        echo "<html lang=\"en_US\"><head><title>{$i_uHTTPStatus} {$i_nstErrorName}</title></head><body><h1>$i_uHTTPStatus $i_nstErrorName</h1><p>$i_nstErrorText</p></body></html>";
    }


    /** @noinspection PhpUnusedParameterInspection */
    protected function errorPath( int $i_uHTTPStatus ) : ?string {
        return null;
    }


    protected function uri() : string {
        return Server::requestUri();
    }


}
