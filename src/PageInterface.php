<?php


declare( strict_types = 1 );


namespace JDWX\Web;


interface PageInterface extends \Stringable {


    public function echo() : void;


    public function getContentType() : string;


    public function render() : string;


    public function stream() : \Generator;


}