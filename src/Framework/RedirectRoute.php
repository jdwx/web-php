<?php


declare( strict_types = 1 );


namespace JDWX\Web\Framework;


class RedirectRoute implements RouteInterface {


    private string $stUri;

    private string $stTarget;

    private int $uStatus;

    private bool $bExact;


    public function __construct( RouterInterface $router ) {}


    public static function make( RouterInterface $i_router, string $i_stUri, string $i_stTarget, int $i_uStatus,
                                 bool            $i_bExact ) : self {
        $route = new self( $i_router );
        $route->stUri = $i_stUri;
        $route->stTarget = $i_stTarget;
        $route->uStatus = $i_uStatus;
        $route->bExact = $i_bExact;
        return $route;
    }


    public function allowPathInfo() : bool {
        return ! $this->bExact;
    }


    public function handle( string $i_stUri, string $i_stPath, array $i_rUriParameters ) : ?ResponseInterface {
        return Response::redirect( $this->target( $i_stUri ), $this->uStatus );
    }


    public function status() : int {
        return $this->uStatus;
    }


    public function target( string $i_stUri ) : string {
        if ( $this->bExact ) {
            return $this->stTarget;
        }
        return $this->stTarget . substr( $i_stUri, strlen( $this->stUri ) );
    }


}
