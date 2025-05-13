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
