<?php


declare( strict_types = 1 );


namespace JDWX\Web\Example;


use JDWX\Web\Framework\AbstractRoute;
use JDWX\Web\Framework\Response;
use JDWX\Web\Framework\ResponseInterface;


class ExampleRouteStream extends AbstractRoute {


    protected function handleGET( string $i_stUri, string $i_stPath, array $i_rUriParameters ) : ?ResponseInterface {
        return Response::eventStream( $this->events() );
    }


    /** @return \Generator<string, mixed> */
    private function events() : \Generator {
        $count = 0;
        while ( true ) {
            yield 'counter' => [ 'count' => $count++ ];
            if ( $count > 20 ) {
                break;
            }
            usleep( 1000000 ); // Sleep for 1 second
        }
    }


}
