<?php


declare( strict_types = 1 );


namespace JDWX\Web\Pages;


use JDWX\Json\Json;
use JDWX\Web\Flush;


class SimpleStreamPage extends AbstractStreamPage {


    public function __construct( private readonly \Generator $gen ) {
        parent::__construct();
    }


    public function stream() : iterable {
        foreach ( $this->gen as $stEventType => $xEventData ) {
            if ( ! is_string( $xEventData ) ) {
                $xEventData = Json::encode( $xEventData );
            }
            yield "event: {$stEventType}\ndata: {$xEventData}\n\n";
            yield new Flush();
        }
    }


}
