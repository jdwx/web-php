<?php


declare( strict_types = 1 );


namespace JDWX\Web\Panels;


use Stringable;


trait YieldTrait {


    /**
     * @param string|iterable<string|Stringable>|Stringable $i_chunk
     * @return iterable<string|Stringable>
     */
    protected function yield( string|iterable $i_chunk ) : iterable {
        if ( is_string( $i_chunk ) ) {
            yield $i_chunk;
            return;
        }
        foreach ( $i_chunk as $stChunk ) {
            yield $stChunk;
        }
    }


}