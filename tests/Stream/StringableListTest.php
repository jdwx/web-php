<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Stream;


use JDWX\Web\Stream\SimpleStringable;
use JDWX\Web\Stream\StringableList;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Stringable;


#[CoversClass( StringableList::class )]
final class StringableListTest extends TestCase {


    public function testAppend() : void {
        $el = new StringableList();
        $el->append( 'Bar', [ 'Baz', 'Qux' ] );
        self::assertEquals( 'BarBazQux', strval( $el ) );

        $child = new StringableList( 'Quux' );
        $el = new StringableList( 'Foo' );
        $el->append( 'Bar', [ 'Baz', $child, [ 'Qux', null ] ] );
        self::assertSame( 'FooBarBazQuuxQux', strval( $el ) );
    }


    public function testAppendChild() : void {
        $list = new StringableList( 'Foo' );
        $list->appendChild( 'Bar' );
        self::assertSame( 'FooBar', strval( $list ) );

        $list = new StringableList( 'Foo' );
        $list->appendChild( null )
            ->appendChild( new StringableList( 'Bar' ) );
        self::assertSame( 2, $list->countChildren() );
        self::assertSame( 'FooBar', strval( $list ) );
    }


    public function testAsList() : void {
        $baz = new SimpleStringable( 'Baz' );
        $stl = new StringableList( [ 'Foo', 'Bar', $baz ] );
        self::assertSame( [ 'Foo', 'Bar', $baz ], $stl->asList() );
    }


    public function testAsListForNested() : void {
        $bar = new SimpleStringable( 'Bar' );
        $stl = new StringableList( [ 'Foo', $bar ] );
        $stl2 = new StringableList( [ 'Baz', $stl ] );
        self::assertSame( [ 'Baz', $stl ], $stl2->asList() );
    }


    public function testChildren() : void {
        $el = new StringableList( [ 'foo', 'bar', 'baz' ] );
        self::assertSame( [ 'foo', 'bar', 'baz' ], iterator_to_array( $el->children(), false ) );
    }


    public function testCountChildren() : void {
        $el1 = new SimpleStringable();
        $el2 = new SimpleStringable();
        $el3 = 'Foo';
        $el4 = new SimpleStringable( 'Bar' );
        $list = new StringableList( [ $el1, $el2, $el3, $el4 ] );
        self::assertSame( 4, $list->countChildren() );
    }


    public function testHasChildren() : void {
        $el = new StringableList();
        self::assertFalse( $el->hasChildren() );
        $el->appendChild( 'Foo' );
        self::assertTrue( $el->hasChildren() );
    }


    public function testNthChild() : void {
        $baz = new SimpleStringable( 'Baz' );
        $qux = new SimpleStringable( 'Qux' );
        $list = new StringableList( [ 'Foo', 'Bar', $baz, $qux ] );
        self::assertSame( 'Foo', strval( $list->nthChild( 0 ) ) );
        self::assertSame( 'Bar', strval( $list->nthChild( 1 ) ) );
        self::assertSame( $baz, $list->nthChild( 2 ) );
        self::assertSame( $qux, $list->nthChild( 3 ) );
        self::assertNull( $list->nthChild( 4 ) );
    }


    public function testPrependChild() : void {
        $el = new StringableList( 'Bar' );
        $el->prependChild( 'Foo' );
        self::assertSame( 'FooBar', strval( $el ) );

        $el = new StringableList( 'Bar' );
        $el->prependChild( null )
            ->prependChild( new StringableList( 'Foo' ) );
        self::assertSame( 'FooBar', strval( $el ) );
    }


    public function testRemoveAllChildren() : void {
        $el = new StringableList( [ 'foo', 'bar', 'baz' ] );
        $el->removeAllChildren();
        self::assertSame( '', strval( $el ) );
    }


    public function testRemoveChildForNotPresent() : void {
        $child = new SimpleStringable( 'Foo' );
        $parent = new StringableList( [ 'Bar', 'Baz' ] );
        $parent->removeChild( $child );
        self::assertSame( 'BarBaz', strval( $parent ) );
    }


    public function testRemoveChildForString() : void {
        $el = new StringableList( [ 'Foo', 'Bar', 'Baz' ] );
        $el->removeChild( 'Bar' );
        self::assertSame( 'FooBaz', strval( $el ) );
    }


    public function testRemoveChildForStringable() : void {
        $child = new SimpleStringable( 'Foo' );
        $parent = new StringableList( [ 'Bar', $child, 'Baz' ] );
        $parent->removeChild( $child );
        self::assertSame( 'BarBaz', strval( $parent ) );
    }


    public function testRemoveChildren() : void {
        $el = new StringableList( [ 'Foo', 'Bar', 'Baz', new SimpleStringable( 'Bar' ) ] );
        $fn = function ( string|Stringable $child ) : bool {
            return 'Bar' === strval( $child );
        };
        $el->removeChildren( $fn );
        self::assertSame( 'FooBaz', strval( $el ) );
    }


    public function testRemoveNthChild() : void {
        $el = new StringableList( [ 'Foo', 'Bar', 'Baz' ] );
        $el->removeNthChild();
        self::assertSame( 'BarBaz', strval( $el ) );

        $el = new StringableList( [ 'Foo', 'Bar', 'Baz' ] );
        $el->removeNthChild( 1 );
        self::assertSame( 'FooBaz', strval( $el ) );

        $el = new StringableList( [ 'Foo', 'Bar', 'Baz' ] );
        $el->removeNthChild( 2 );
        self::assertSame( 'FooBar', strval( $el ) );

        $el = new StringableList( [ 'Foo', 'Bar', 'Baz' ] );
        $el->removeNthChild( 3 );
        self::assertSame( 'FooBarBaz', strval( $el ) );
    }


    public function testStream() : void {
        $baz = new SimpleStringable( 'Baz' );
        $qux = new SimpleStringable( 'Qux' );
        $list = new StringableList( [ 'Foo', 'Bar', $baz, $qux ] );
        $r = [];
        foreach ( $list->stream() as $el ) {
            $r[] = $el;
        }
        self::assertSame( [ 'Foo', 'Bar', $baz, $qux ], $r );
    }


}
