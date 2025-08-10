<?php


declare( strict_types = 1 );


namespace JDWX\Web\Framework;


use Ds\Set;
use JDWX\Web\Pages\PageInterface;


interface ResponseInterface extends \Stringable {


    /**
     * Returns the value of a header given its name or null if it does not exist.
     * The header name is case-insensitive.
     */
    public function getHeader( string $i_stHeaderName ) : ?string;


    /** @return Set<string> */
    public function getHeaders() : Set;


    public function getPage() : PageInterface;


    public function getStatusCode() : int;


}