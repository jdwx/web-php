<?php


declare( strict_types = 1 );


namespace JDWX\Web\Framework;


use Ds\Map;
use JDWX\Web\RequestInterface;
use Psr\Log\LoggerInterface;


class Router extends AbstractRouter {


    /** @var Map<string, string|RouteInterface> */
    private Map $routes;

    private bool $bRootIsPrefix = false;


    public function __construct( ?LoggerInterface  $i_logger = null, ?HttpError $i_error = null,
                                 ?RequestInterface $i_req = null ) {
        parent::__construct( $i_logger, $i_error, $i_req );
        $this->routes = new Map();
    }


    /**
     * @inheritDoc
     */
    public function route() : bool {
        $stUri = $this->uri();

        # The expeditious case is an exact match.
        if ( $this->routes->hasKey( $stUri ) ) {
            return $this->handle( $stUri, '' );
        }

        # Check for a prefix match.
        # Note: Only routes that end in a slash can be prefixes.
        # The exception is the root route, which is not a prefix
        # by default.
        $nstLongest = null;
        foreach ( $this->routes as $stRouteUri => $routeClass ) {
            if ( ! str_ends_with( $stRouteUri, '/' ) ) {
                continue;
            }
            if ( '/' === $stRouteUri && ! $this->bRootIsPrefix ) {
                continue;
            }
            if ( ! str_starts_with( $stUri, $stRouteUri ) ) {
                continue;
            }
            if ( is_string( $nstLongest ) && strlen( $stRouteUri ) <= strlen( $nstLongest ) ) {
                continue;
            }
            $nstLongest = $stRouteUri;
        }

        if ( is_string( $nstLongest ) ) {
            $stPath = substr( $stUri, strlen( $nstLongest ) - 1 );
            return $this->handle( $nstLongest, $stPath );
        }

        # Otherwise: new route, who dis?
        return false;
    }


    protected function addRoute( string $i_stUri, string|RouteInterface $i_route ) : void {
        if ( is_string( $i_route ) && ! class_exists( $i_route ) ) {
            throw new \InvalidArgumentException( "Class {$i_route} does not exist" );
        }
        if ( ! is_subclass_of( $i_route, RouteInterface::class ) ) {
            throw new \InvalidArgumentException( "Class {$i_route} is not a route." );
        }
        if ( $this->routes->hasKey( $i_stUri ) ) {
            if ( ! is_string( $i_route ) ) {
                $i_route = $i_route::class;
            }
            throw new \InvalidArgumentException( "Route {$i_stUri} already exists for {$i_route}." );
        }
        $this->routes->put( $i_stUri, $i_route );
    }


    protected function handle( string $i_stRouteUri, string $i_stPath ) : bool {
        $route = $this->routes->get( $i_stRouteUri );
        if ( is_string( $route ) ) {
            $route = new $route( $this );
        }
        assert( $route instanceof RouteInterface );
        $response = $route->handle( $i_stRouteUri, $i_stPath );
        if ( ! $response instanceof ResponseInterface ) {
            return false;
        }
        return $this->respond( $response );
    }


    protected function setRootIsPrefix( bool $i_bRootIsPrefix ) : void {
        $this->bRootIsPrefix = $i_bRootIsPrefix;
    }


}
