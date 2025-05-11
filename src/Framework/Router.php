<?php


declare( strict_types = 1 );


namespace JDWX\Web\Framework;


use JDWX\Web\RequestInterface;
use Psr\Log\LoggerInterface;


class Router extends RouteRouter {


    public function __construct( ?LoggerInterface  $i_logger = null, ?HttpError $i_error = null,
                                 ?RequestInterface $i_req = null ) {
        parent::__construct( new MapRouteManager(), $i_logger, $i_error, $i_req );
    }


}
