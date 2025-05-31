<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Shims;


use JDWX\Web\Framework\RouteInterface;


require_once __DIR__ . '/MyRouterTrait.php';


trait MyRouteRouterTrait {


    use MyRouterTrait;


    public function addRoutePub( string $i_stUri, string|RouteInterface $i_route ) : void {
        $this->addRoute( $i_stUri, $i_route );
    }


    public function setRootIsPrefixPub() : void {
        $this->setRootIsPrefix( true );
    }


    abstract protected function addRoute( string $i_stUri, string|RouteInterface $i_route ) : void;


    abstract protected function setRootIsPrefix( bool $i_b ) : void;


}