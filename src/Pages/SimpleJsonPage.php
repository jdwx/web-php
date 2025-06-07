<?php


declare( strict_types = 1 );


namespace JDWX\Web\Pages;


use JDWX\Json\Json;
use JDWX\Stream\StringStreamTrait;
use JsonSerializable;


class SimpleJsonPage extends AbstractJsonPage {


    use StringStreamTrait;


    /**
     * @param int|mixed[]|string|float|bool|JsonSerializable|null $i_content
     * @param bool $i_bPretty
     * @throws \JsonException
     */
    public function __construct( int|array|string|float|bool|null|JsonSerializable $i_content,
                                 bool                                              $i_bPretty = false ) {
        parent::__construct();
        if ( $i_bPretty ) {
            $this->setStream( Json::encodePretty( $i_content ) . "\n" );
        } else {
            $this->setStream( Json::encode( $i_content ) . "\n" );
        }
    }


}