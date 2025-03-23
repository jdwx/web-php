<?php


declare( strict_types = 1 );


namespace Shims;


use JDWX\Web\Framework\RouteInterface;
use JDWX\Web\Framework\Router;


class MyRouter extends Router {


    public function addRoutePub( string $i_stUri, string|RouteInterface $i_route ) : void {
        parent::addRoute( $i_stUri, $i_route );
    }


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
        parent::setRootIsPrefix( true );
    }


}
