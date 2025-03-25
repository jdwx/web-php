<?php /** @noinspection PhpUnused */


declare( strict_types = 1 );


namespace JDWX\Web\Framework;


use Ds\Set;
use JDWX\Log\StderrLogger;
use JDWX\Web\Framework\Exceptions\HttpStatusException;
use JDWX\Web\Framework\Exceptions\MethodNotAllowedException;
use JDWX\Web\Framework\Exceptions\NotFoundException;
use JDWX\Web\Http;
use JDWX\Web\JsonPage;
use JDWX\Web\PageInterface;
use JDWX\Web\Request;
use JDWX\Web\RequestInterface;
use JDWX\Web\SimpleHtmlPage;
use JDWX\Web\TextPage;
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
        if ( $this->request->isGET() ) {
            return;
        }
        throw new MethodNotAllowedException( $i_nstText ?? 'GET required' );
    }


    public function assertPOST( ?string $i_nstText = null ) : void {
        if ( $this->request->isPOST() ) {
            return;
        }
        throw new MethodNotAllowedException( $i_nstText ?? 'POST required' );
    }


    public function getHttpError() : HttpError {
        return $this->error;
    }


    public function logger() : LoggerInterface {
        return $this->logger;
    }


    public function request() : RequestInterface {
        return $this->request;
    }


    public function run() : void {
        try {
            if ( $this->route() ) {
                return;
            }
            throw new NotFoundException( 'Page not found: ' . $this->path() );
        } catch ( HttpStatusException $e ) {
            $this->logger->error( $e->getMessage(), [
                'code' => $e->getCode(),
                'method' => $this->request()->method(),
                'uri' => $this->uri(),
                'display' => $e->display() ?? '(nothing)',
            ] );
            $this->error->showException( $e );
        }
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


    protected function path() : string {
        return $this->uriParts()->path();
    }


    protected function respond( ResponseInterface $i_response ) : true {
        $page = $i_response->getPage();
        $setHeaders = $this->setHeaders->merge( $i_response->getHeaders() );
        $setHeaders->add( 'Content-Type: ' . $page->getContentType() );
        foreach ( $setHeaders as $header ) {
            Http::setHeader( $header );
        }
        Http::setResponseCode( $i_response->getStatusCode() );
        $page->echo();
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
        return $this->respondPage( new JsonPage( $i_content, $i_bPretty ), $i_uStatus, $i_setHeaders );
    }


    /** @param ?Set<string> $i_setHeaders */
    protected function respondPage( PageInterface $i_page, int $i_uStatus = 200,
                                    ?Set          $i_setHeaders = null ) : true {
        return $this->respond( new Response( $i_page, $i_uStatus, $i_setHeaders ) );
    }


    /** @param ?Set<string> $i_setHeaders */
    protected function respondText( string $i_stContent, int $i_uStatus = 200,
                                    ?Set   $i_setHeaders = null ) : true {
        return $this->respondPage( new TextPage( $i_stContent ), $i_uStatus, $i_setHeaders );
    }


    protected function uri() : string {
        return $this->request()->uri();
    }


    protected function uriParts() : UrlParts {
        return $this->request()->uriParts();
    }


}
