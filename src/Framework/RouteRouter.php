<?php


declare( strict_types = 1 );


namespace JDWX\Web\Framework;


use InvalidArgumentException;
use JDWX\Web\RequestInterface;
use LogicException;
use Psr\Log\LoggerInterface;


class RouteRouter extends AbstractRouter {


    private bool $bRootIsPrefix = false;


    public function __construct( private readonly RouteManagerInterface $routes,
                                 ?LoggerInterface                       $i_logger = null, ?HttpError $i_error = null,
                                 ?RequestInterface                      $i_req = null ) {
        parent::__construct( $i_logger, $i_error, $i_req );
    }


    public function addStaticRoute( string $i_stUri, string $i_stFileName,
                                    string $i_stContentType = 'application/octet-stream' ) : void {
        $this->addRoute( $i_stUri, $this->makeStaticRoute( $i_stFileName, $i_stContentType ) );
    }


    public function makeStaticRoute( string $i_stFileName, string $i_stContentType ) : RouteInterface {
        return StaticFileRoute::make( $this, $i_stFileName, $i_stContentType );
    }


    /**
     * @inheritDoc
     * @param ?string $i_nstUriOverride Can be used to override the Uri
     *                           from the request, for example, to remap
     *                           it to a different route.
     */
    public function route( ?string $i_nstUriOverride = null ) : bool {
        $stUri = $i_nstUriOverride ?? $this->path();

        $rBestMatches = [];
        $uBestMatchLength = 0;
        foreach ( $this->routes->matches( $stUri ) as $match ) {

            if ( ! $match->isExact() ) {

                # Discard matches for the root URI that have path info unless
                # the root URI is explicitly allowed to be a prefix.
                if ( '/' === $match->stUri && ! $this->bRootIsPrefix ) {
                    continue;
                }

                # Otherwise, a prefix match is only valid if the URI
                # ends with a slash.
                if ( ! str_ends_with( $match->stUri, '/' ) ) {
                    continue;
                }
            }

            $uMatchLength = strlen( $match->stUri );
            if ( $uMatchLength < $uBestMatchLength ) {
                continue;
            }
            if ( $uMatchLength > $uBestMatchLength ) {
                $rBestMatches = [];
                $uBestMatchLength = $uMatchLength;
            }
            $rBestMatches[] = $match;

        }

        if ( 0 === count( $rBestMatches ) ) {
            return false;
        }

        # Multiple matches of equal length are ambiguous and therefore
        # not allowed.
        if ( 1 < count( $rBestMatches ) ) {
            $u = count( $rBestMatches ) - 1;
            throw new LogicException( "URI is ambiguous ({$u} matches): {$stUri}" );
        }

        return $this->handle( $rBestMatches[ 0 ] );
    }


    protected function addRedirect( string $i_stUri, string $i_stTarget, int $i_uStatus = 301,
                                    bool   $i_bExact = true ) : void {
        $route = RedirectRoute::make( $this, $i_stUri, $i_stTarget, $i_uStatus, $i_bExact );
        $this->routes->add( $i_stUri, $route );
    }


    protected function addRoute( string $i_stUri, string|RouteInterface $i_route ) : void {
        if ( is_string( $i_route ) && ! class_exists( $i_route ) ) {
            throw new InvalidArgumentException( "Class {$i_route} does not exist" );
        }
        if ( is_string( $i_route ) && ! is_subclass_of( $i_route, RouteInterface::class ) ) {
            throw new InvalidArgumentException( "Class {$i_route} is not a route." );
        }
        $this->routes->add( $i_stUri, $i_route );
    }


    protected function handle( RouteMatch $i_match ) : bool {
        $route = $i_match->route( $this );
        if ( ! $i_match->isExact() && ! $route->allowPathInfo() ) {
            return false;
        }
        $response = $route->handle(
            $i_match->stUri, $i_match->stPathInfo, $i_match->rParameters
        );
        if ( ! $response instanceof ResponseInterface ) {
            return false;
        }
        return $this->respond( $response );
    }


    protected function setRootIsPrefix( bool $i_bRootIsPrefix ) : void {
        $this->bRootIsPrefix = $i_bRootIsPrefix;
    }


}