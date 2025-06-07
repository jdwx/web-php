<?php


declare( strict_types = 1 );


namespace JDWX\Web\Pages;


use JDWX\Stream\StreamInterface;
use Stringable;


interface PageInterface extends Stringable, StreamInterface {


    public function echo() : void;


    public function getContentType() : string;


    /** @return iterable<string> */
    public function getHeaders() : iterable;


    public function render() : string;


}