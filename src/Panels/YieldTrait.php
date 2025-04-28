<?php


declare( strict_types = 1 );


namespace JDWX\Web\Panels;


trait YieldTrait {


    /**
     * @param string|iterable<string> $i_chunk
     * @return iterable<string>
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