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
     *
     * This is commonly used for testing when you want to compare the
     * contents to a static array. It does *not* recurse into stream
     * elements that are both Stringable and iterable (i.e., nested
     * streams). See StreamHelper::asList().
     */
    public function asList() : array {
        return iterator_to_array( $this->stream(), false );
    }


    /** @return iterable<string|Stringable> */
    abstract public function stream() : iterable;


}