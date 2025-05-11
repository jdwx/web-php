<?php


declare( strict_types = 1 );


namespace Framework;


use InvalidArgumentException;
use JDWX\Web\Framework\MapRouteManager;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use Shims\MyRoute;


#[UsesClass( MapRouteManager::class )]
final class MapRouteManagerTest extends TestCase {


    public function testAddForDuplicate() : void {
        $router = new MapRouteManager();
        $router->add( '/foo', MyRoute::class );
        self::expectException( InvalidArgumentException::class );
        $router->add( '/foo', MyRoute::class );
    }


    public function testMatchesForMultipleMatches() : void {
        $router = new MapRouteManager();
        $router->add( '/foo', MyRoute::class );
        $router->add( '/foobar', MyRoute::class );
        $router->add( '/foo/bar', MyRoute::class );
        $router->add( '/foo/baz/', MyRoute::class );

        $matches = iterator_to_array( $router->matches( '/foo' ), false );
        self::assertCount( 1, $matches );
        self::assertSame( '/foo', $matches[ 0 ]->stUri );
        self::assertSame( '', $matches[ 0 ]->stPathInfo );
        self::assertSame( MyRoute::class, $matches[ 0 ]->route );

        # Does not match /foo.
        $matches = iterator_to_array( $router->matches( '/foobar' ), false );
        self::assertCount( 1, $matches );
        self::assertSame( '/foobar', $matches[ 0 ]->stUri );
        self::assertSame( '', $matches[ 0 ]->stPathInfo );
        self::assertSame( MyRoute::class, $matches[ 0 ]->route );

        # Does not match /foobar.
        /** @noinspection SpellCheckingInspection */
        $matches = iterator_to_array( $router->matches( '/foobarbaz' ), false );
        self::assertCount( 0, $matches );

        $matches = iterator_to_array( $router->matches( '/foo/bar' ), false );
        self::assertCount( 1, $matches );
        self::assertSame( '/foo/bar', $matches[ 0 ]->stUri );
        self::assertSame( '', $matches[ 0 ]->stPathInfo );
        self::assertSame( MyRoute::class, $matches[ 0 ]->route );

        $matches = iterator_to_array( $router->matches( '/foo/bar/baz' ), false );
        self::assertCount( 0, $matches );

        $matches = iterator_to_array( $router->matches( '/foo/baz/qux' ), false );
        self::assertCount( 1, $matches );
        self::assertSame( '/foo/baz/', $matches[ 0 ]->stUri );
        self::assertSame( '/qux', $matches[ 0 ]->stPathInfo );
        self::assertSame( MyRoute::class, $matches[ 0 ]->route );

    }


    public function testMatchesForNoMatches() : void {
        $router = new MapRouteManager();
        $router->add( '/foo', MyRoute::class );
        $router->add( '/bar', MyRoute::class );
        $matches = iterator_to_array( $router->matches( '/baz' ), false );
        self::assertCount( 0, $matches );
    }


    public function testMatchesForOneMatch() : void {
        $router = new MapRouteManager();
        $router->add( '/foo', MyRoute::class );
        $router->add( '/bar', MyRoute::class );
        $matches = iterator_to_array( $router->matches( '/foo' ), false );
        self::assertCount( 1, $matches );
        self::assertSame( '/foo', $matches[ 0 ]->stUri );
        self::assertSame( MyRoute::class, $matches[ 0 ]->route );
    }


}