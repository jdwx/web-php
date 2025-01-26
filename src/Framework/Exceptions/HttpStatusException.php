<?php


declare( strict_types = 1 );


namespace JDWX\Web\Framework\Exceptions;


class HttpStatusException extends \RuntimeException {


    public function __construct( int                      $iCode, string $stMessage = '',
                                 private readonly ?string $nstDisplay = null,
                                 ?\Throwable              $i_ePrevious = null ) {
        parent::__construct( $stMessage, $iCode, $i_ePrevious );
    }


    public function display() : ?string {
        return $this->nstDisplay;
    }


}
