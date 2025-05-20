<?php


declare( strict_types = 1 );


namespace JDWX\Web\Stream;


use Stringable;


trait NestedStreamableTrait {


    /** @return iterable<string|Stringable> */
    public function stream() : iterable {
        yield from YieldHelper::yield( $this->prefix() );
        yield from YieldHelper::yield( $this->infix() );
        yield from YieldHelper::yield( $this->postfix() );
    }


    /** @return iterable<string|Stringable>|string|Stringable */
    abstract protected function infix() : iterable|string|Stringable;


    /** @return iterable<string|Stringable>|string|Stringable */
    abstract protected function postfix() : iterable|string|Stringable;


    /** @return iterable<string|Stringable>|string|Stringable */
    abstract protected function prefix() : iterable|string|Stringable;


}
