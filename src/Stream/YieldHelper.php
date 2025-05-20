<?php


declare( strict_types = 1 );


namespace JDWX\Web\Stream;


use Stringable;


final class YieldHelper {


    /**
     * @param iterable<string|Stringable>|string|Stringable $i_chunk
     * @return iterable<string|Stringable>
     */
    public static function yield( iterable|string|Stringable $i_chunk ) : iterable {
        if ( is_string( $i_chunk ) || $i_chunk instanceof Stringable ) {
            yield $i_chunk;
            return;
        }
        yield from $i_chunk;
    }


    /**
     * @param iterable<string|Stringable|iterable<string|Stringable>>|string|Stringable $i_chunk
     * @return iterable<string|Stringable>
     */
    public static function yieldDeep( iterable|string|Stringable $i_chunk ) : iterable {
        if ( is_string( $i_chunk ) || $i_chunk instanceof Stringable ) {
            yield $i_chunk;
            return;
        }
        foreach ( $i_chunk as $stChunk ) {
            yield from self::yieldDeep( $stChunk );
        }
    }


}