<?php


declare( strict_types = 1 );


namespace JDWX\Web\Pages;


use Generator;


class SimpleTextPage extends AbstractTextPage {


    public function __construct( private readonly string $stContent ) {
        parent::__construct();
    }


    public function stream() : Generator {
        yield $this->stContent;
    }


}