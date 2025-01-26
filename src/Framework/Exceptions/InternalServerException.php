<?php


declare( strict_types = 1 );


namespace JDWX\Web\Framework\Exceptions;


class InternalServerException extends HttpStatusException {


    public function __construct( string      $stMessage = '', ?string $i_nstDisplay = null,
                                 ?\Throwable $i_ePrevious = null ) {
        parent::__construct( 500, $stMessage, $i_nstDisplay, $i_ePrevious );
    }


}
