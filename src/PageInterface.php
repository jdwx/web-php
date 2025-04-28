<?php


declare( strict_types = 1 );


namespace JDWX\Web;


use Stringable;


interface PageInterface extends Stringable {


    public function echo() : void;


    public function getContentType() : string;


    public function render() : string;


    /** @return iterable<string> */
    public function stream() : iterable;


}