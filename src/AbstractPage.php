<?php


declare( strict_types = 1 );


namespace JDWX\Web;


abstract class AbstractPage implements PageInterface {


    public function __construct( private readonly string $stContentType ) {}


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


    public function render( ?string $i_nstLanguage = null ) : string {
        return implode( '', iterator_to_array( $this->stream( $i_nstLanguage ), false ) );
    }


}