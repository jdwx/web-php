<?php


declare( strict_types = 1 );


namespace JDWX\Web\Framework;


use JDWX\Web\RequestInterface;
use Psr\Log\LoggerInterface;


class TrieRouter extends RouteRouter {


    public function __construct( ?LoggerInterface $i_logger = null,
                                 ?HttpError       $i_error = null, ?RequestInterface $i_req = null,
                                 bool             $i_bAllowVariables = true, bool $i_bAllowExtra = true ) {
        parent::__construct( new TrieRouteManager( $i_bAllowVariables, $i_bAllowExtra ), $i_logger, $i_error, $i_req );
    }


}