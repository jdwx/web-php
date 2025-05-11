<?php


declare( strict_types = 1 );


namespace JDWX\Web\Example;


use JDWX\Web\Framework\AbstractRoute;
use JDWX\Web\Framework\Response;
use JDWX\Web\Framework\ResponseInterface;


class ExampleRouteRedirect extends AbstractRoute {


    protected function handleGET( string $i_stUri, string $i_stPath, array $i_rUriParameters ) : ?ResponseInterface {
        return Response::redirectTemporaryWithSameMethod( '/' );
    }


    protected function handlePOST( string $i_stUri, string $i_stPath, array $i_rUriParameters ) : ?ResponseInterface {
        return $this->handleGET( $i_stUri, $i_stPath, $i_rUriParameters );
    }


}