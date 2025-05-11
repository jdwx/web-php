<?php


declare( strict_types = 1 );


namespace JDWX\Web\Framework;


interface RouteManagerInterface {


    public function add( string $i_stUri, string|RouteInterface $i_route ) : void;


    /** @return iterable<RouteMatch> */
    public function matches( string $i_stUri ) : iterable;


}