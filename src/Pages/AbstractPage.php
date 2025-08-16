<?php


declare( strict_types = 1 );


namespace JDWX\Web\Pages;


use JDWX\Stream\StringableStreamTrait;
use JDWX\Web\Flush;


abstract class AbstractPage implements PageInterface {


    use StringableStreamTrait;


    public function __construct( private readonly string $stContentType,
                                 private ?string         $nstCharset = null ) {}


    public function echo() : void {
        foreach ( $this->stream() as $stChunk ) {
            if ( $stChunk instanceof Flush ) {
                ob_flush();
                continue;
            }
            echo $stChunk;
        }
    }


    public function getCharset() : ?string {
        return $this->nstCharset;
    }


    public function getContentType() : string {
        return $this->stContentType;
    }


    public function getFullContentType() : string {
        $nstCharset = $this->getCharset();
        if ( ! is_string( $nstCharset ) ) {
            return $this->getContentType();
        }
        return $this->getContentType() . "; charset={$nstCharset}";
    }


    /**
     * @return iterable<string> Extra headers to set for this page.
     */
    public function getHeaders() : iterable {
        yield 'Content-Type: ' . $this->getFullContentType();
    }


    public function hasCharset() : bool {
        return ! empty( $this->nstCharset );
    }


    public function render() : string {
        return $this->__toString();
    }


    public function setCharset( ?string $i_nstCharset ) : void {
        $this->nstCharset = $i_nstCharset;
    }


}