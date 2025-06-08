<?php


declare( strict_types = 1 );


namespace JDWX\Web\Pages;


use JDWX\Stream\StreamHelper;
use Stringable;


class SimpleBinaryPage extends AbstractBinaryPage {


    /** @param iterable<string|Stringable>|string|Stringable $data */
    public function __construct( private readonly iterable|string|Stringable $data,
                                 ?string                                     $i_nstContentType = null ) {
        parent::__construct( $i_nstContentType );
    }


    public function stream() : iterable {
        yield from StreamHelper::yieldDeep( $this->data );
    }


}
