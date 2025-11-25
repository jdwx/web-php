<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Framework;


use JDWX\Web\Framework\RouteMatch;
use JDWX\Web\Framework\TrieRouteManager;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;


#[CoversClass( TrieRouteManager::class )]
final class TrieRouteManagerTest extends TestCase {


    public function testAddForDuplicates() : void {
        $mgr = new TrieRouteManager( true, true );
        $mgr->add( 'foo/bar/baz', 'QUX' );
        $this->expectException( \InvalidArgumentException::class );
        $mgr->add( 'foo/bar/baz', 'QUUX' );
    }


    public function testMatchesForNoMatches() : void {
        $mgr = new TrieRouteManager( true, true );
        $mgr->add( '/foo', 'FOO' );
        $mgr->add( '/bar', 'BAR' );
        $r = iterator_to_array( $mgr->matches( '/baz' ), false );
        self::assertCount( 0, $r );

        $r = iterator_to_array( $mgr->matches( '/foobar' ), false );
        self::assertCount( 0, $r );
    }


    public function testMatchesForOneMatch() : void {
        $mgr = new TrieRouteManager( true, true );
        $mgr->add( '/foo', 'FOO' );
        $mgr->add( '/bar', 'BAR' );
        $r = iterator_to_array( $mgr->matches( '/foo' ), false );
        self::assertCount( 1, $r );
        assert( $r[ 0 ] instanceof RouteMatch );
        self::assertSame( 'FOO', $r[ 0 ]->route );
    }


    public function testMatchesForRest() : void {
        $mgr = new TrieRouteManager( true, true );
        $mgr->add( '/foo/', 'FOO' );
        $r = iterator_to_array( $mgr->matches( '/foo/bar' ), false );
        self::assertCount( 1, $r );
        assert( $r[ 0 ] instanceof RouteMatch );
        self::assertSame( 'FOO', $r[ 0 ]->route );
        self::assertSame( '/foo/', $r[ 0 ]->stUri );
        self::assertSame( '/bar', $r[ 0 ]->stPathInfo );

    }


}
