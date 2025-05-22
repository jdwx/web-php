<?php


declare( strict_types = 1 );


namespace Shims;


use JDWX\Web\Framework\RouteInterface;


/**
 * Can't declare an abstract route() method in this class because
 * static analysis loses its mind.
 */
trait MyRouterTrait {


    public function addRoutePub( string $i_stUri, string|RouteInterface $i_route ) : void {
        $this->addRoute( $i_stUri, $i_route );
    }


    /** @suppress PhanUndeclaredMethod */
    public function routeOutput( mixed ...$x ) : ?string {
        ob_start();
        $b = $this->route( ...$x );
        $st = ob_get_clean();
        return $b ? $st : null;
    }


    /** @suppress PhanUndeclaredMethod */
    public function routeQuiet( mixed ...$x ) : bool {
        ob_start();
        $b = $this->route( ...$x );
        ob_end_clean();
        return $b;
    }


    public function setRootIsPrefixPub() : void {
        $this->setRootIsPrefix( true );
    }


    abstract protected function addRoute( string $i_stUri, string|RouteInterface $i_route ) : void;


    abstract protected function setRootIsPrefix( bool $i_b ) : void;


}