<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Framework;


require_once __DIR__ . '/../Shims/MyRoute.php';
require_once __DIR__ . '/../Shims/MyRouteRouterTestBase.php';
require_once __DIR__ . '/../Shims/MyRouterInterface.php';
require_once __DIR__ . '/../Shims/MyRouteRouterTrait.php';


use JDWX\Web\Framework\TrieRouter;
use JDWX\Web\RequestInterface;
use JDWX\Web\Tests\Shims\MyRouterInterface;
use JDWX\Web\Tests\Shims\MyRouteRouterTestBase;
use JDWX\Web\Tests\Shims\MyRouteRouterTrait;
use PHPUnit\Framework\Attributes\CoversClass;


#[CoversClass( TrieRouter::class )]
final class TrieRouterTest extends MyRouteRouterTestBase {


    protected function newRouter( ?RequestInterface $i_req = null ) : MyRouterInterface {
        $i_req ??= $this->newRequest( 'GET', '/test' );
        return new class( i_req: $i_req ) extends TrieRouter implements MyRouterInterface {


            use MyRouteRouterTrait;
        };
    }


}