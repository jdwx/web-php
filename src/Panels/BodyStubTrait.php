<?php


declare( strict_types = 1 );


namespace JDWX\Web\Panels;


trait BodyStubTrait {


    public function bodyEarly() : iterable|string {
        return '';
    }


    public function bodyLate() : iterable|string {
        return '';
    }


    public function first() : void {}


    public function head() : iterable|string {
        return '';
    }


    public function last() : void {}


}