<?php


declare( strict_types = 1 );


namespace Panels;


use JDWX\Web\Panels\Element;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Stringable;


#[CoversClass( Element::class )]
final class ElementTest extends TestCase {


    public function testAppend() : void {
        $child = new Element( i_children: 'Quux' );
        $el = new Element( i_children: 'Foo' );
        $el->append( 'Bar', [ 'Baz', $child, [ 'Qux', null ] ] );
        self::assertSame( '<div>FooBarBaz<div>Quux</div>Qux</div>', strval( $el ) );
    }


    public function testAppendChild() : void {
        $el = new Element( i_children: 'foo' );
        $el->appendChild( 'bar' );
        self::assertSame( '<div>foobar</div>', strval( $el ) );

        $el = new Element( i_children: 'foo' );
        $el->appendChild( null )
            ->appendChild( new Element( i_children: 'bar' ) );
        self::assertSame( '<div>foo<div>bar</div></div>', strval( $el ) );
    }


    public function testChildElements() : void {
        $elChild = new Element( i_children: 'foo' );
        $elParent = new Element( i_children: [ $elChild, 'bar' ] );
        self::assertSame( [ $elChild ], iterator_to_array( $elParent->childElements(), false ) );
    }


    public function testChildElementsWithFilter() : void {
        $elChild1 = ( new Element( i_children: 'foo' ) )->setAttribute( 'pick' );
        $elChild2 = new Element( i_children: 'bar' );
        $elChild3 = ( new Element( i_children: 'baz' ) )->setAttribute( 'pick' );
        $elChild4 = new Element( i_children: 'qux' );
        $elParent =
            new Element( i_children: [ 'Quux', $elChild1, 'Corge', $elChild2, 'Grault', $elChild3, 'Garply', $elChild4 ] );
        $fn = function ( Element $child ) : bool {
            return $child->hasAttribute( 'pick' );
        };
        self::assertSame( [ $elChild1, $elChild3 ], iterator_to_array( $elParent->childElements( $fn ), false ) );
    }


    public function testChildren() : void {
        $el = new Element( i_children: [ 'foo', 'bar', 'baz' ] );
        self::assertSame( [ 'foo', 'bar', 'baz' ], iterator_to_array( $el->children(), false ) );
    }


    public function testNthChild() : void {
        $el = new Element( i_children: [ 'foo', 'bar', 'baz' ] );
        self::assertSame( 'foo', strval( $el->nthChild( 0 ) ) );
        self::assertSame( 'bar', strval( $el->nthChild( 1 ) ) );
        self::assertSame( 'baz', strval( $el->nthChild( 2 ) ) );
        self::assertNull( $el->nthChild( 3 ) );
    }


    public function testNthChildElement() : void {
        $elChild1 = new Element( i_children: 'foo' );
        $elChild2 = new Element( i_children: 'bar' );
        $el = new Element( i_children: [ 'baz', $elChild1, 'qux', $elChild2, 'corge' ] );
        self::assertSame( $elChild1, $el->nthChildElement( 0 ) );
        self::assertSame( $elChild2, $el->nthChildElement( 1 ) );
        self::assertNull( $el->nthChildElement( 2 ) );
    }


    public function testPrependChild() : void {
        $el = new Element( i_children: 'bar' );
        $el->prependChild( 'foo' );
        self::assertSame( '<div>foobar</div>', strval( $el ) );

        $el = new Element( i_children: 'bar' );
        $el->prependChild( null )
            ->prependChild( new Element( i_children: 'foo' ) );
        self::assertSame( '<div><div>foo</div>bar</div>', strval( $el ) );
    }


    public function testRemoveAllChildren() : void {
        $el = new Element( i_children: [ 'foo', 'bar', 'baz' ] );
        $el->removeAllChildren();
        self::assertSame( '<div></div>', strval( $el ) );
    }


    public function testRemoveChildForElement() : void {
        $child = new ELement( i_children: 'Foo' );
        $parent = new Element( i_children: [ 'Bar', $child, 'Baz' ] );
        $parent->removeChild( $child );
        self::assertSame( '<div>BarBaz</div>', strval( $parent ) );
    }


    public function testRemoveChildForNotPresent() : void {
        $child = new Element( i_children: 'Foo' );
        $parent = new Element( i_children: [ 'Bar', 'Baz' ] );
        $parent->removeChild( $child );
        self::assertSame( '<div>BarBaz</div>', strval( $parent ) );
    }


    public function testRemoveChildForString() : void {
        $el = new Element( i_children: [ 'Foo', 'Bar', 'Baz' ] );
        $el->removeChild( 'Bar' );
        self::assertSame( '<div>FooBaz</div>', strval( $el ) );
    }


    public function testRemoveChildren() : void {
        $el = new Element( i_children: [ 'Foo', 'Bar', 'Baz' ] );
        $fn = function ( string|Stringable $child ) : bool {
            return 'Bar' === strval( $child );
        };
        $el->removeChildren( $fn );
        self::assertSame( '<div>FooBaz</div>', strval( $el ) );
    }


    public function testRemoveNthChild() : void {
        $el = new Element( i_children: [ 'Foo', 'Bar', 'Baz' ] );
        $el->removeNthChild();
        self::assertSame( '<div>BarBaz</div>', strval( $el ) );

        $el = new Element( i_children: [ 'Foo', 'Bar', 'Baz' ] );
        $el->removeNthChild( 1 );
        self::assertSame( '<div>FooBaz</div>', strval( $el ) );

        $el = new Element( i_children: [ 'Foo', 'Bar', 'Baz' ] );
        $el->removeNthChild( 2 );
        self::assertSame( '<div>FooBar</div>', strval( $el ) );

        $el = new Element( i_children: [ 'Foo', 'Bar', 'Baz' ] );
        $el->removeNthChild( 3 );
        self::assertSame( '<div>FooBarBaz</div>', strval( $el ) );
    }


    public function testRemoveNthChildElement() : void {
        $elChild1 = new Element( i_children: 'Foo' );
        $elChild2 = new Element( i_children: 'Bar' );
        $el = new Element( i_children: [ 'Baz', $elChild1, 'Qux', $elChild2, 'Corge' ] );
        $el->removeNthChildElement();
        self::assertSame( '<div>BazQux<div>Bar</div>Corge</div>', strval( $el ) );

        $el = new Element( i_children: [ 'Baz', $elChild1, 'Qux', $elChild2, 'Corge' ] );
        $el->removeNthChildElement( 1 );
        self::assertSame( '<div>Baz<div>Foo</div>QuxCorge</div>', strval( $el ) );

        $el = new Element( i_children: [ 'Baz', $elChild1, 'Qux', $elChild2, 'Corge' ] );
        $el->removeNthChildElement( 2 );
        self::assertSame( '<div>Baz<div>Foo</div>Qux<div>Bar</div>Corge</div>', strval( $el ) );
    }


    public function testToStringForIterable() : void {
        $el = new Element( i_children: [ 'foo', 'bar' ] );
        self::assertSame( '<div>foobar</div>', strval( $el ) );
    }


    public function testToStringForNoClose() : void {
        $el = new Element( i_children: 'foo' );
        $el->setAlwaysClose( false );
        self::assertSame( '<div>foo</div>', strval( $el ) );

        $el = new Element();
        $el->setAlwaysClose( false );
        self::assertSame( '<div>', strval( $el ) );
    }


    public function testToStringForString() : void {
        $el = new Element( i_children: 'foo' );
        self::assertSame( '<div>foo</div>', strval( $el ) );
    }


    public function testToStringForStringable() : void {
        $el = new Element( i_children: new class() {


            public function __toString() : string {
                return 'foo';
            }


        } );
        self::assertSame( '<div>foo</div>', strval( $el ) );
    }


}
