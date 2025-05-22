<?php


declare( strict_types = 1 );


namespace Shims;


use JDWX\Web\Framework\RouteRouter;


require_once __DIR__ . '/MyRouteRouterTrait.php';


class MyRouteRouter extends RouteRouter {


    use MyRouteRouterTrait;
}
