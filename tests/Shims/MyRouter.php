<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Shims;


require_once __DIR__ . '/MyRouterInterface.php';
require_once __DIR__ . '/MyRouteRouterTrait.php';


use JDWX\Web\Framework\Router;


class MyRouter extends Router implements MyRouterInterface {


    use MyRouteRouterTrait;
}
