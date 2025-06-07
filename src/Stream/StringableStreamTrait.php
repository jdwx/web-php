<?php


declare( strict_types = 1 );


namespace JDWX\Web\Stream;


/** Implements Stringable and IteratorAggregate based on stream(). */
trait StringableStreamTrait {


    use StreamTrait;


    public function __toString() : string {
        return join( '', $this->asList() );
    }


    /** @return iterable<string> */
    public function streamStrings() : iterable {
        foreach ( $this->stream() as $chunk ) {
            yield strval( $chunk );
        }
    }


}