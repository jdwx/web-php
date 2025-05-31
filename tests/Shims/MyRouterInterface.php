<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Shims;


use JDWX\Web\Framework\RouteInterface;
use JDWX\Web\Framework\RouterInterface;


interface MyRouterInterface extends RouterInterface {


    public function addRoutePub( string $i_stUri, string|RouteInterface $i_route ) : void;


    public function routeOutput() : ?string;


    public function routeQuiet() : bool;


    public function setRootIsPrefixPub() : void;


}