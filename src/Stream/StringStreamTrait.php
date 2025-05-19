<?php


declare( strict_types = 1 );


namespace JDWX\Web\Stream;


trait StringStreamTrait {


    private string $stStream;


    public function setStream( string $i_stStream ) : void {
        $this->stStream = $i_stStream;
    }


    /** @return iterable<string> */
    public function stream() : iterable {
        yield $this->stStream ?? '';
    }


}