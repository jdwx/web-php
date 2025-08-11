<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Shims;


use JDWX\Web\Framework\RouteInterface;


require_once __DIR__ . '/MyRouterTrait.php';


trait MyRouteRouterTrait {


    use MyRouterTrait;


    public function addRedirectPub( string $i_stUri, string $i_stTarget, int $i_uStatus = 301,
                                    bool   $i_bExact = true ) : void {
        $this->addRedirect( $i_stUri, $i_stTarget, $i_uStatus, $i_bExact );
    }


    public function addRoutePub( string $i_stUri, string|RouteInterface $i_route ) : void {
        $this->addRoute( $i_stUri, $i_route );
    }


    public function setRootIsPrefixPub() : void {
        $this->setRootIsPrefix( true );
    }


    abstract protected function addRedirect( string $i_stUri, string $i_stTarget, int $i_uStatus = 301,
                                             bool   $i_bExact = true ) : void;


    abstract protected function addRoute( string $i_stUri, string|RouteInterface $i_route ) : void;


    abstract protected function setRootIsPrefix( bool $i_b ) : void;


}