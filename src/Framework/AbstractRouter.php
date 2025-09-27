<?php /** @noinspection PhpUnused */


declare( strict_types = 1 );


namespace JDWX\Web\Framework;


use Ds\Set;
use JDWX\Log\StderrLogger;
use JDWX\Web\Framework\Exceptions\BadRequestException;
use JDWX\Web\Framework\Exceptions\HttpStatusException;
use JDWX\Web\Framework\Exceptions\MethodNotAllowedException;
use JDWX\Web\Framework\Exceptions\NotFoundException;
use JDWX\Web\Http;
use JDWX\Web\Pages\PageInterface;
use JDWX\Web\Pages\SimpleHtmlPage;
use JDWX\Web\Pages\SimpleJsonPage;
use JDWX\Web\Pages\SimpleTextPage;
use JDWX\Web\Request;
use JDWX\Web\RequestInterface;
use JDWX\Web\ServerInterface;
use JDWX\Web\UrlParts;
use JsonSerializable;
use Psr\Log\LoggerInterface;


abstract class AbstractRouter implements RouterInterface {


    use HttpTrait;


    protected HttpError $error;

    protected LoggerInterface $logger;

    private RequestInterface $request;


    /** @var Set<string> */
    private Set $setHeaders;


    public function __construct( ?LoggerInterface  $i_logger = null,
                                 ?HttpError        $i_error = null,
                                 ?RequestInterface $i_req = null ) {
        $this->error = $i_error ?? new HttpError();
        $this->logger = $i_logger ?? new StderrLogger();
        $this->request = $i_req ?? Request::getGlobal();
        $this->setHeaders = new Set();
    }


    public function assertGET( ?string $i_nstText = null ) : void {
        $this->assertMethod( 'GET', $i_nstText );
    }


    public function assertPOST( ?string $i_nstText = null ) : void {
        $this->assertMethod( 'POST', $i_nstText );
    }


    public function getHttpError() : HttpError {
        return $this->error;
    }


    public function logger() : LoggerInterface {
        return $this->logger;
    }


    public function methodNotAllowed( ?string $i_nstUri = null, ?string $i_nstPath = null,
                                      ?string $i_nstMessage = null ) : never {
        $i_nstMessage ??= sprintf( 'Method {{ method }} not allowed for URI: %s, Path: %s',
            $i_nstUri ?? $this->uri(), $i_nstPath ?? $this->path() );
        throw new MethodNotAllowedException( $this->method(), $i_nstMessage );
    }


    public function request() : RequestInterface {
        return $this->request;
    }


    public function run() : void {
        try {
            if ( ! $this->request()->validateUri() ) {
                throw new BadRequestException( 'Invalid URI' );
            }
            if ( $this->route() ) {
                return;
            }
            throw new NotFoundException( 'Page not found: ' . $this->path() );
        } catch ( HttpStatusException $e ) {
            $this->handleHttpStatusException( $e );
        }
    }


    public function server() : ServerInterface {
        return $this->request()->server();
    }


    /**
     * Adds a header that will always be sent with every response. Headers
     * may be specified as either a string (e.g. "X-Foo: bar") or as a
     * header and value (e.g. "X-Foo" and "bar").
     */
    protected function addHeader( string $i_stHeader, ?string $i_stValue = null ) : void {
        if ( is_string( $i_stValue ) ) {
            $this->setHeaders->add( "{$i_stHeader}: {$i_stValue}" );
        } else {
            $this->setHeaders->add( $i_stHeader );
        }
    }


    protected function assertMethod( string $i_stMethod, ?string $i_nstText = null ) : void {
        if ( $this->request->method() === $i_stMethod ) {
            return;
        }
        $this->methodNotAllowed( $i_nstText ?? "{$i_stMethod} required" );
    }


    protected function handleHttpStatusException( HttpStatusException $i_e ) : void {
        $this->logger->error( $i_e->getMessage(), [
            'class' => $i_e::class,
            'code' => $i_e->getCode(),
            'method' => $this->request()->method(),
            'uri' => $this->uri(),
            'display' => $i_e->display() ?? '(nothing)',
            'trace' => $i_e->getTraceAsString(),
            'at' => $i_e->getFile() . ':' . $i_e->getLine(),
        ] );
        $this->error->showException( $i_e );
    }


    protected function method() : string {
        return $this->request->method();
    }


    protected function path() : string {
        return $this->uriParts()->path();
    }


    protected function respond( ResponseInterface $i_response ) : true {
        $page = $i_response->getPage();
        $setHeaders = $this->setHeaders->merge( $i_response->getHeaders() );
        $setHeaders = $setHeaders->merge( $page->getHeaders() );
        foreach ( $setHeaders as $header ) {
            Http::setHeader( $header );
        }
        Http::setResponseCode( $i_response->getStatusCode() );
        if ( ! $this->request->isHEAD() ) {
            $page->echo();
        }
        return true;
    }


    /** @param ?Set<string> $i_setHeaders */
    protected function respondHtml( string $i_stContent, int $i_uStatus = 200,
                                    ?Set   $i_setHeaders = null ) : true {
        return $this->respondPage( new SimpleHtmlPage( $i_stContent ), $i_uStatus, $i_setHeaders );
    }


    /**
     * @param int|mixed[]|string|float|bool|JsonSerializable|null $i_content
     * @param int $i_uStatus
     * @param bool $i_bPretty
     * @param ?Set<string> $i_setHeaders
     * @return true
     */
    protected function respondJson( int|array|string|float|bool|null|JsonSerializable $i_content,
                                    int                                               $i_uStatus = 200,
                                    bool                                              $i_bPretty = false,
                                    ?Set                                              $i_setHeaders = null ) : true {
        return $this->respondPage( new SimpleJsonPage( $i_content, $i_bPretty ), $i_uStatus, $i_setHeaders );
    }


    /** @param ?Set<string> $i_setHeaders */
    protected function respondPage( PageInterface $i_page, int $i_uStatus = 200,
                                    ?Set          $i_setHeaders = null ) : true {
        return $this->respond( new Response( $i_page, $i_uStatus, $i_setHeaders ) );
    }


    /** @param ?Set<string> $i_setHeaders */
    protected function respondText( string $i_stContent, int $i_uStatus = 200,
                                    ?Set   $i_setHeaders = null ) : true {
        return $this->respondPage( new SimpleTextPage( $i_stContent ), $i_uStatus, $i_setHeaders );
    }


    protected function uri() : string {
        return $this->request()->uri();
    }


    protected function uriParts() : UrlParts {
        return $this->request()->uriParts();
    }


}
