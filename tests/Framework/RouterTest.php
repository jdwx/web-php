<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Framework;


require_once __DIR__ . '/../Shims/MyRoute.php';
require_once __DIR__ . '/../Shims/MyRouteManager.php';
require_once __DIR__ . '/../Shims/MyRouteRouterTestBase.php';
require_once __DIR__ . '/../Shims/MyRouter.php';


use JDWX\Web\Framework\Router;
use JDWX\Web\Framework\RouteRouter;
use JDWX\Web\RequestInterface;
use JDWX\Web\Tests\Shims\MyRouter;
use JDWX\Web\Tests\Shims\MyRouterInterface;
use JDWX\Web\Tests\Shims\MyRouteRouterTestBase;
use PHPUnit\Framework\Attributes\CoversClass;


#[CoversClass( Router::class )]
#[CoversClass( RouteRouter::class )]
final class RouterTest extends MyRouteRouterTestBase {


    protected function newRouter( ?RequestInterface $i_req = null ) : MyRouterInterface {
        $i_req ??= $this->newRequest( 'GET', '/test' );
        return new MyRouter( i_req: $i_req );
    }


}
