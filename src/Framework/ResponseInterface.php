<?php


declare( strict_types = 1 );


namespace JDWX\Web\Framework;


use Ds\Set;
use JDWX\Web\Pages\PageInterface;


interface ResponseInterface extends \Stringable {


    /** @return Set<string> */
    public function getHeaders() : Set;


    public function getPage() : PageInterface;


    public function getStatusCode() : int;


}