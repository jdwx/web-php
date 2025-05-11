<?php


declare( strict_types = 1 );


namespace JDWX\Web\Example;


use JDWX\Web\Framework\AbstractRoute;
use JDWX\Web\Framework\Response;
use JDWX\Web\Framework\ResponseInterface;


class ExampleRouteAdd extends AbstractRoute {


    protected function handlePOST( string $i_stUri, string $i_stPath, array $i_rUriParameters ) : ?ResponseInterface {
        $req = $this->request();
        $num1 = $req->postEx( 'num1' )->asFloat();
        $num2 = $req->postEx( 'num2' )->asFloat();
        $sum = $num1 + $num2;
        return Response::text( strval( $sum ) );
    }


}
