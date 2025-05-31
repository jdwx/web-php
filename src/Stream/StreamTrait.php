<?php


declare( strict_types = 1 );


namespace JDWX\Web\Stream;


use Stringable;


/**
 * Implements the rest of StreamInterface in terms of the
 * stream() method.
 */
trait StreamTrait {


    /**
     * @return list<string|Stringable>
     * This is commonly used for testing when you want to compare the
     * contents to a static array.
     */
    public function asArray() : array {
        return iterator_to_array( $this->stream(), false );
    }


    /** @return iterable<string|Stringable> */
    abstract public function stream() : iterable;


}