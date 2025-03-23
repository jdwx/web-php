<?php


declare( strict_types = 1 );


namespace JDWX\Web;


use Generator;


class TextPage extends AbstractPage {


    public function __construct( private readonly string $stContent ) {
        parent::__construct( 'text/plain' );
    }


    public function stream() : Generator {
        yield $this->stContent;
    }


}