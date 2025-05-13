<?php


declare( strict_types = 1 );


namespace Panels;


use JDWX\Web\Panels\Element;
use PHPUnit\Framework\TestCase;


class ElementTest extends TestCase {


    public function testAppendChild() : void {
        $el = new Element( i_body: 'foo' );
        $el->appendChild( 'bar' );
        self::assertSame( '<div>foobar</div>', strval( $el ) );
    }


    public function testChildElements() : void {
        $elChild = new Element( i_body: 'foo' );
        $elParent = new Element( i_body: [ $elChild, 'bar' ] );
        self::assertSame( [ $elChild ], iterator_to_array( $elParent->childElements(), false ) );
    }


    public function testChildren() : void {
        $el = new Element( i_body: [ 'foo', 'bar', 'baz' ] );
        self::assertSame( [ 'foo', 'bar', 'baz' ], iterator_to_array( $el->children(), false ) );
    }


    public function testNthChild() : void {
        $el = new Element( i_body: [ 'foo', 'bar', 'baz' ] );
        self::assertSame( 'foo', strval( $el->nthChild( 0 ) ) );
        self::assertSame( 'bar', strval( $el->nthChild( 1 ) ) );
        self::assertSame( 'baz', strval( $el->nthChild( 2 ) ) );
        self::assertNull( $el->nthChild( 3 ) );
    }


    public function testNthChildElement() : void {
        $elChild1 = new Element( i_body: 'foo' );
        $elChild2 = new Element( i_body: 'bar' );
        $el = new Element( i_body: [ 'baz', $elChild1, 'qux', $elChild2, 'corge' ] );
        self::assertSame( $elChild1, $el->nthChildElement( 0 ) );
        self::assertSame( $elChild2, $el->nthChildElement( 1 ) );
        self::assertNull( $el->nthChildElement( 2 ) );
    }


    public function testPrependChild() : void {
        $el = new Element( i_body: 'bar' );
        $el->prependChild( 'foo' );
        self::assertSame( '<div>foobar</div>', strval( $el ) );
    }


    public function testToStringForIterable() : void {
        $el = new Element( i_body: [ 'foo', 'bar' ] );
        self::assertSame( '<div>foobar</div>', strval( $el ) );
    }


    public function testToStringForString() : void {
        $el = new Element( i_body: 'foo' );
        self::assertSame( '<div>foo</div>', strval( $el ) );
    }


    public function testToStringForStringable() : void {
        $el = new Element( i_body: new class() {


            public function __toString() : string {
                return 'foo';
            }


        } );
        self::assertSame( '<div>foo</div>', strval( $el ) );
    }


}
