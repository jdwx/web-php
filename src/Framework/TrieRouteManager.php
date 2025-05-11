<?php


declare( strict_types = 1 );


namespace JDWX\Web\Framework;


use InvalidArgumentException;
use JDWX\Trie\Trie;


class TrieRouteManager implements RouteManagerInterface {


    private Trie $routes;


    public function __construct( bool $i_bAllowVariables = true, bool $i_bAllowExtra = true ) {
        $this->routes = new Trie( $i_bAllowVariables, $i_bAllowExtra );
    }


    public function add( string $i_stUri, string|RouteInterface $i_route ) : void {
        if ( $this->routes->has( $i_stUri ) ) {
            throw new InvalidArgumentException( "Route already exists for '{$i_stUri}'" );
        }
        $this->routes->add( $i_stUri, $i_route );
    }


    /** @return iterable<RouteMatch> */
    public function matches( string $i_stUri ) : iterable {

        foreach ( $this->routes->match( $i_stUri ) as $match ) {
            $stUri = $match->path();
            $stRest = $match->rest();
            if ( $stRest && ! str_ends_with( $stUri, '/' ) ) {
                continue;
            }
            if ( $stRest && ! str_starts_with( $stRest, '/' ) ) {
                $stRest = '/' . $stRest;
            }
            yield new RouteMatch( $stUri, $match->value(), $stRest, $match->variables() );
        }
    }


}