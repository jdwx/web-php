<?php


declare( strict_types = 1 );


namespace JDWX\Web\Framework\Exceptions;


class NotImplementedException extends MethodException {


    public function __construct( string      $stRawMethod, ?string $stMessage = null,
                                 ?string     $i_nstDisplay = null,
                                 ?\Throwable $i_ePrevious = null ) {
        $stMessage = $stMessage ?? 'Method {{ method }} is not implemented.';
        $i_nstDisplay = $i_nstDisplay ?? 'Method not implemented.';
        parent::__construct( 501, $stRawMethod, $stMessage, $i_nstDisplay, $i_ePrevious );
    }


}
