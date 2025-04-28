<?php


declare( strict_types = 1 );


namespace JDWX\Web;


abstract class AbstractPage implements PageInterface {


    public function __construct( private readonly string $stContentType ) { }


    public function __toString() : string {
        return $this->render();
    }


    public function echo() : void {
        foreach ( $this->stream() as $stChunk ) {
            echo $stChunk;
        }
    }


    public function getContentType() : string {
        return $this->stContentType;
    }


    public function render() : string {
        return implode( '', iterator_to_array( $this->stream(), false ) );
    }


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