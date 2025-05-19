<?php


declare( strict_types = 1 );


namespace JDWX\Web\Pages;


abstract class AbstractJsonPage extends AbstractPage {


    public function __construct() {
        parent::__construct( 'application/json' );
    }


}
