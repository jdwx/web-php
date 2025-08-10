<?php


declare( strict_types = 1 );


namespace JDWX\Web\Framework;


use JDWX\Log\BufferLogger;
use JDWX\Web\Backends\MockServer;
use JDWX\Web\Request;
use JDWX\Web\RequestInterface;
use JDWX\Web\ServerInterface;
use LogicException;
use Psr\Log\LoggerInterface;


/**
 * This is NOT A PRODUCTION CLASS.
 *
 * This class is provided to simplify unit testing of individual routes.
 */
class RouteTestRouter implements RouterInterface {


    public LoggerInterface $log;

    public RequestInterface $req;

    public ServerInterface $srv;

    /** @var array<string, list<string>|string> A list of parameters embedded in the URL. /user/$username/profile */
    public array $rUrlParameters = [];


    public function __construct( string $i_stMethod = 'GET' ) {
        $this->log = new BufferLogger();
        $this->srv = ( new MockServer() )->withRequestMethod( $i_stMethod );
        $this->req = Request::synthetic( i_server: $this->srv );
    }


    public function assertGET() : void {
        if ( ! $this->req->isGET() ) {
            throw new LogicException( 'Expected GET request, got ' . $this->req->method() );
        }
    }


    public function assertPOST() : void {
        if ( ! $this->req->isPOST() ) {
            throw new LogicException( 'Expected HEAD request, got ' . $this->req->method() );
        }
    }


    public function getHttpError() : HttpError {
        return new HttpError();
    }


    public function logger() : LoggerInterface {
        return $this->log;
    }


    public function methodNotAllowed( ?string $i_nstUri = null, ?string $i_nstPath = null, ?string $i_nstMessage = null ) : never {
        throw new LogicException( 'Method not allowed.' );
    }


    public function request() : RequestInterface {
        return $this->req;
    }


    /** @codeCoverageIgnore */
    public function route() : bool {
        return true;
    }


    /** @codeCoverageIgnore */
    public function run() : void {}


    public function server() : ServerInterface {
        return $this->srv;
    }


    public function test( RouteInterface $i_route ) : ?ResponseInterface {
        return $i_route->handle( $this->req->uri(), $this->req->path(), $this->rUrlParameters );
    }


}
