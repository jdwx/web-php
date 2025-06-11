<?php


declare( strict_types = 1 );


namespace JDWX\Web\Framework;


readonly class RouteMatch {


    /** @param array<string, list<string>|string> $rParameters */
    public function __construct( public string $stUri, public string|RouteInterface $route,
                                 public string $stPathInfo, public array $rParameters ) {}


    public function isExact() : bool {
        return '' === $this->stPathInfo;
    }


    public function route( RouterInterface $i_router ) : RouteInterface {
        $route = $this->route;
        if ( is_string( $route ) ) {
            $route = new $this->route( $i_router );
            assert( $route instanceof RouteInterface );
        }
        return $route;
    }


}