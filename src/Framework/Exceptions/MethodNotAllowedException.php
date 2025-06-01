<?php


declare( strict_types = 1 );


namespace JDWX\Web\Framework\Exceptions;


class MethodNotAllowedException extends MethodException {


    public function __construct( string      $stRawMethod, string $i_stMessage = '', ?string $i_nstDisplay = null,
                                 ?\Throwable $i_ePrevious = null ) {
        parent::__construct( 405, $stRawMethod, $i_stMessage, $i_nstDisplay, $i_ePrevious );
    }


}
