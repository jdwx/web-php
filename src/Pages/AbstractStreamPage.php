<?php


declare( strict_types = 1 );


namespace JDWX\Web\Pages;


abstract class AbstractStreamPage extends AbstractPage {


    public function __construct() {
        parent::__construct( 'text/event-stream' );
    }


}
