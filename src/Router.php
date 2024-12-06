<?php /** @noinspection PhpUnused */


declare( strict_types = 1 );


namespace JDWX\Web;


use Psr\Log\LoggerInterface;


abstract class Router implements IRouter {


    protected HttpError $error;


    final public function __construct( protected ?LoggerInterface $logger = null, ?HttpError $i_error = null ) {
        $this->error = $i_error ?? new HttpError();
    }


    /** @param mixed[] $i_rContext */
    protected function abort( string $i_stError, array $i_rContext = [] ) : never {
        $this->logger?->warning( $i_stError, $i_rContext );
        exit( 10 );
    }


    /** @deprecated Use $this->error->show() */
    protected function error( int     $i_uHTTPStatus, ?string $i_nstErrorName = null,
                              ?string $i_nstErrorText = null ) : void {
        $this->error->show( $i_uHTTPStatus, $i_nstErrorName, $i_nstErrorText );
    }


    protected function uri() : string {
        return Server::requestUri();
    }


}
