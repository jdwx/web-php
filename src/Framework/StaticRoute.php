<?php


declare( strict_types = 1 );


namespace JDWX\Web\Framework;


use JDWX\Strict\OK;


/**
 * This class is useful for sticking small static files in otherwise dynamic locations, like
 * robots.txt or favicon.ico.
 */
class StaticRoute extends AbstractRoute {


    private string $stFileName;


    private string $stContentType;


    public static function make( RouterInterface $i_router, string $i_stFileName,
                                 ?string         $i_nstContentType = null ) : self {
        $route = new self( $i_router );
        $route->stFileName = $i_stFileName;
        $route->stContentType = $i_nstContentType
            ?? $route->inferContentTypeEx( $i_stFileName, 'application/octet-stream' );
        return $route;
    }


    protected function handleGET( string $i_stUri, string $i_stPath, array $i_rUriParameters ) : ?ResponseInterface {
        return Response::binary( OK::file_get_contents( $this->stFileName ), 200, $this->stContentType );
    }


    use StaticTrait;
}
