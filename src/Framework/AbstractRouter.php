<?php /** @noinspection PhpUnused */


declare( strict_types = 1 );


namespace JDWX\Web\Framework;


use JDWX\Log\StderrLogger;
use JDWX\Web\Framework\Exceptions\HttpStatusException;
use JDWX\Web\Framework\Exceptions\MethodNotAllowedException;
use JDWX\Web\Framework\Exceptions\NotFoundException;
use JDWX\Web\Request;
use JDWX\Web\RequestInterface;
use JDWX\Web\UrlParts;
use Psr\Log\LoggerInterface;


abstract class AbstractRouter implements RouterInterface {


    use HttpTrait;


    protected HttpError $error;

    protected LoggerInterface $logger;

    private RequestInterface $request;


    public function __construct( ?LoggerInterface  $i_logger = null,
                                 ?HttpError        $i_error = null,
                                 ?RequestInterface $i_req = null ) {
        $this->error = $i_error ?? new HttpError();
        $this->logger = $i_logger ?? new StderrLogger();
        $this->request = $i_req ?? Request::getGlobal();
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


    protected function path() : string {
        return $this->uriParts()->path();
    }


    protected function uri() : string {
        return $this->request()->uri();
    }


    protected function uriParts() : UrlParts {
        return $this->request()->uriParts();
    }


}
