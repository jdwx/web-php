<?php


declare( strict_types = 1 );


namespace Shims;


require_once __DIR__ . '/MyHttpError.php';


use JDWX\Web\Framework\HttpError;
use JDWX\Web\Framework\StaticShim;
use JDWX\Web\IRequest;


class MyStaticShim extends StaticShim {


    public function __construct( ?string   $i_nstDocumentRoot = null, ?HttpError $i_error = null,
                                 ?IRequest $i_req = null ) {
        parent::__construct( $i_nstDocumentRoot, $i_error ?? new MyHttpError(), $i_req );
    }


    public function setAuthoritative( bool $b ) : void {
        $this->bAuthoritative = $b;
    }


}
