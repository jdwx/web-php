<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Shims;


use JDWX\Web\Framework\RouteInterface;
use JDWX\Web\Framework\RouteManagerInterface;
use JDWX\Web\Framework\RouteMatch;
use LogicException;


class MyRouteManager implements RouteManagerInterface {


    /** @var array<string, RouteMatch|list<RouteMatch>> */
    public array $routes = [];


    public function add( string $i_stUri, string|RouteInterface $i_route ) : void {
        throw new LogicException( 'Not implemented' );
    }


    public function matches( string $i_stUri ) : iterable {
        if ( ! isset( $this->routes[ $i_stUri ] ) ) {
            return;
        }
        $matches = $this->routes[ $i_stUri ];
        if ( $matches instanceof RouteMatch ) {
            yield $matches;
            return;
        }
        yield from $matches;
    }


}