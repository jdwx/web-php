<?php


declare( strict_types = 1 );


namespace JDWX\Web\Framework;


use JDWX\Strict\OK;


/**
 * This class is useful for sticking small static files in otherwise dynamic locations, like
 * robots.txt or favicon.ico.
 */
class StaticFileRoute extends AbstractStaticRoute {


    private string $stFileName;


    public static function make( RouterInterface $i_router, string $i_stFileName,
                                 ?string         $i_nstContentType = null ) : self {
        $route = new self( $i_router );
        $route->stFileName = $i_stFileName;
        $route->setContentType( $i_nstContentType
            ?? $route->inferContentTypeEx( $i_stFileName, 'application/octet-stream' ) );
        return $route;
    }


    protected function getContent() : string {
        return OK::file_get_contents( $this->stFileName );
    }


}
