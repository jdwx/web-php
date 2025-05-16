<?php


declare( strict_types = 1 );


namespace JDWX\Web\Panels;


use Stringable;


trait BodyStubTrait {


    /** @return iterable<string|Stringable>|string|Stringable */
    public function bodyEarly() : iterable|string|Stringable {
        return '';
    }


    /** @return iterable<string|Stringable>|string|Stringable */
    public function bodyLate() : iterable|string|Stringable {
        return '';
    }


    public function first() : void {}


    /** @return iterable<string|Stringable>|string|Stringable */
    public function head() : iterable|string|Stringable {
        return '';
    }


    public function last() : void {}


}