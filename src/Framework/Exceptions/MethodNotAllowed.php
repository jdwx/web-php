<?php


declare( strict_types = 1 );


namespace JDWX\Web\Framework\Exceptions;


class MethodNotAllowed extends HttpStatusException {


    public function __construct( string $i_stMessage = '', ?string $i_nstDisplay = null, ?\Throwable $i_ePrevious = null ) {
        parent::__construct( 405, $i_stMessage, $i_nstDisplay, $i_ePrevious );
    }


}
