<?php


declare( strict_types = 1 );


namespace JDWX\Web;


use Generator;
use JDWX\Json\Json;
use JsonSerializable;


class JsonPage extends AbstractPage {


    private string $stContent;


    public function __construct( int|array|string|float|bool|null|JsonSerializable $i_content,
                                 bool                                              $i_bPretty = false ) {
        parent::__construct( 'application/json' );
        if ( $i_bPretty ) {
            $this->stContent = Json::encodePretty( $i_content );
        } else {
            $this->stContent = Json::encode( $i_content );
        }
    }


    public function stream() : Generator {
        yield $this->stContent . "\n";
    }


}