<?php


declare( strict_types = 1 );


namespace JDWX\Web\Pages;


abstract class AbstractBinaryPage extends AbstractPage {


    public function __construct( ?string $i_nstContentType = null ) {
        parent::__construct( $i_nstContentType ?? 'application/octet-stream' );
    }


}
