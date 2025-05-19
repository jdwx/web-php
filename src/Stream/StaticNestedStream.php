<?php


declare( strict_types = 1 );


namespace JDWX\Web\Stream;


use Stringable;


class StaticNestedStream extends AbstractNestedStringableStream {


    /**
     * @param iterable<string|Stringable>|string|Stringable $infix
     * @param iterable<string|Stringable>|string|Stringable $prefix
     * @param iterable<string|Stringable>|string|Stringable $suffix
     */
    public function __construct( public readonly iterable|string|Stringable $infix,
                                 public readonly iterable|string|Stringable $prefix = [],
                                 public readonly iterable|string|Stringable $suffix = [] ) {}


    /** @return iterable<string|Stringable>|string|Stringable */
    public function infix() : iterable|string|Stringable {
        return $this->infix;
    }


    /** @return iterable<string|Stringable>|string|Stringable */
    public function postfix() : iterable|string|Stringable {
        return $this->suffix;
    }


    /** @return iterable<string|Stringable>|string|Stringable */
    public function prefix() : iterable|string|Stringable {
        return $this->prefix;
    }


}
