<?php


declare( strict_types = 1 );


namespace JDWX\Web\Stream;


/**
 * Sometimes, after all that functionality, you just want
 * to return a string.
 */
class StringStream implements StringableStreamInterface {


    use StreamTrait;
    use StringableStreamTrait;
    use StringStreamTrait;


    public function __construct( string $i_stStream ) {
        $this->setStream( $i_stStream );
    }


}
