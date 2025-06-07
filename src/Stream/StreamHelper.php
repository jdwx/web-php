<?php


declare( strict_types = 1 );


namespace JDWX\Web\Stream;


use Stringable;


final class StreamHelper {


    /**
     * @param iterable<string|Stringable|iterable<string|Stringable|iterable<mixed>>>|string|Stringable $i_stream
     * @return list<string|Stringable>
     *
     * This differs from StreamInterface::asList() because it flattens the list all the
     * way down, whereas StreamInterface::asList() doesn't recurse into children that are
     * Stringable, which includes most streams.
     */
    public static function asList( iterable|string|Stringable $i_stream ) : array {
        return iterator_to_array( self::yieldDeep( $i_stream ), false );
    }


    /** @param iterable<string|Stringable|iterable<string|Stringable|iterable<mixed>>>|string|Stringable $i_stream */
    public static function toString( iterable|string|Stringable $i_stream ) : string {
        if ( is_string( $i_stream ) ) {
            return $i_stream;
        }
        if ( $i_stream instanceof Stringable ) {
            return $i_stream->__toString();
        }
        return join( '', self::asList( $i_stream ) );
    }


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
        if ( $i_chunk instanceof StreamInterface ) {
            $i_chunk = $i_chunk->stream();
        }
        if ( is_string( $i_chunk ) || ( $i_chunk instanceof Stringable && ! is_iterable( $i_chunk ) ) ) {
            yield $i_chunk;
            return;
        }
        foreach ( $i_chunk as $stChunk ) {
            yield from self::yieldDeep( $stChunk );
        }
    }


}
