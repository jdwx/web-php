<?php


declare( strict_types = 1 );


namespace Shims;


require_once __DIR__ . '/HttpTesterTrait.php';
require_once __DIR__ . '/MyHttpError.php';


use JDWX\Web\Framework\HttpError;
use JDWX\Web\Framework\StaticShim;
use JDWX\Web\IRequest;


class MyStaticShim extends StaticShim {


    use HttpTesterTrait;


    public function __construct( ?string   $i_nstDocumentRoot = null, ?HttpError $i_error = null,
                                 ?IRequest $i_req = null ) {
        parent::__construct( $i_nstDocumentRoot, $i_error ?? new MyHttpError(), $i_req );
    }


    public function errorStatus() : int {
        $error = $this->error;
        assert( $error instanceof MyHttpError );
        return $error->iStatus;
    }


    public function setAuthoritative( bool $b ) : void {
        $this->bAuthoritative = $b;
    }


}
