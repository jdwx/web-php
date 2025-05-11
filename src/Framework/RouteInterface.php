<?php


declare( strict_types = 1 );


namespace JDWX\Web\Framework;


interface RouteInterface {


    /**
     * RouteInterface objects must be able to be instantiated by
     * the router using only the class name.
     */
    public function __construct( RouterInterface $router );


    /**
     * @param string $i_stUri The URI of the route.
     * @param string $i_stPath The URI of any path after the route.
     * @param array<string, string> $i_rParameters The parameters for the route. (If any.)
     * @return ResponseInterface|null The response object or null to defer
     *                                to the next matching route.
     *
     * If the route is a prefix, the path will be the rest of the URI after the route
     * prefix is removed.
     *
     * In the simplest case, where a route object handles a single URI and isn't
     * a prefix, you can just ignore the parameters entirely.
     */
    public function handle( string $i_stUri, string $i_stPath, array $i_rParameters ) : ?ResponseInterface;


}