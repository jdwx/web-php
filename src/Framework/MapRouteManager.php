<?php


declare( strict_types = 1 );


namespace JDWX\Web\Framework;


use Ds\Map;
use InvalidArgumentException;


class MapRouteManager implements RouteManagerInterface {


    /** @var Map<string, string|RouteInterface> */
    private Map $routes;


    public function __construct() {
        $this->routes = new Map();
    }


    public function add( string $i_stUri, string|RouteInterface $i_route ) : void {
        if ( isset( $this->routes[ $i_stUri ] ) ) {
            throw new InvalidArgumentException( "Route already exists for URI: {$i_stUri}" );
        }
        $this->routes->put( $i_stUri, $i_route );
    }


    /** @return iterable<RouteMatch> */
    public function matches( string $i_stUri ) : iterable {

        # The expeditious case is an exact match.
        if ( $this->routes->hasKey( $i_stUri ) ) {
            yield new RouteMatch( $i_stUri, $this->routes->get( $i_stUri ), '', [] );
            return;
        }

        # Check for a prefix match.
        foreach ( $this->routes as $stRouteUri => $routeClass ) {
            if ( ! str_ends_with( $stRouteUri, '/' ) ) {
                continue;
            }
            if ( ! str_starts_with( $i_stUri, $stRouteUri ) ) {
                continue;
            }
            $stRest = substr( $i_stUri, strlen( $stRouteUri ) - 1 );
            yield new RouteMatch( $stRouteUri, $routeClass, $stRest, [] );
        }

    }


}