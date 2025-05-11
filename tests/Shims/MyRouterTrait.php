<?php


declare( strict_types = 1 );


namespace Shims;


use JDWX\Web\Framework\RouteInterface;


trait MyRouterTrait {


    public function addRoutePub( string $i_stUri, string|RouteInterface $i_route ) : void {
        $this->addRoute( $i_stUri, $i_route );
    }


    abstract public function route() : bool;


    public function routeOutput() : ?string {
        ob_start();
        $b = $this->route();
        $st = ob_get_clean();
        return $b ? $st : null;
    }


    public function routeQuiet() : bool {
        ob_start();
        $b = $this->route();
        ob_end_clean();
        return $b;
    }


    public function setRootIsPrefixPub() : void {
        $this->setRootIsPrefix( true );
    }


    abstract protected function addRoute( string $i_stUri, string|RouteInterface $i_route ) : void;


    abstract protected function setRootIsPrefix( bool $i_b ) : void;


}