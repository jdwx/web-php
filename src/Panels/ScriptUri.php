<?php


declare( strict_types = 1 );


namespace JDWX\Web\Panels;


class ScriptUri extends AbstractScript {


    public function __construct( string $i_stUri ) {
        $this->setSrc( $i_stUri );
    }


}