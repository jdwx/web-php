<?php


declare( strict_types = 1 );


namespace JDWX\Web\Stream;


use Stringable;


/**
 * This is mostly used for testing when you need to see if
 * something works with a Stringable type.
 */
class SimpleStringable implements Stringable {


    public function __construct( public string $st = '' ) {}


    public function __toString() : string {
        return $this->st;
    }


}
