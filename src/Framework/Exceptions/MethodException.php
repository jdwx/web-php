<?php


declare( strict_types = 1 );


namespace JDWX\Web\Framework\Exceptions;


use JDWX\Strict\OK;


class MethodException extends HttpStatusException {


    private readonly string $stMethod;


    public function __construct( int         $iCode, private readonly string $stRawMethod,
                                 string      $stMessage = '',
                                 ?string     $i_nstDisplay = null,
                                 ?\Throwable $i_ePrevious = null ) {
        # Because this may get displayed on the error page, sanitize the method name.
        # Replace any non-alphanumeric characters with underscores.
        $stMethod = OK::preg_replace_string( '/[^a-zA-Z0-9]/', '_', $stRawMethod );
        if ( strlen( $stMethod ) > 16 ) {
            $stMethod = substr( $stMethod, 0, 13 ) . '...';
        }
        $this->stMethod = $stMethod;
        $stMessage = str_replace( '{{ method }}', $stMethod, $stMessage );
        if ( is_string( $i_nstDisplay ) ) {
            $i_nstDisplay = str_replace( '{{ method }}', $stMethod, $i_nstDisplay );
        }

        parent::__construct( $iCode, $stMessage, $i_nstDisplay, $i_ePrevious );
    }


    public function method() : string {
        return $this->stMethod;
    }


    public function rawMethod() : string {
        return $this->stRawMethod;
    }


}
