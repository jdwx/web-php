<?php


declare( strict_types = 1 );


namespace JDWX\Web\Panels;


use Stringable;


abstract class ElementPanel extends AbstractBodyPanel {


    use AttributeTrait;


    public function __construct( private string $stElement = 'div' ) {}


    /** @return iterable<string|Stringable>|string|Stringable */
    public function body() : iterable|string|Stringable {
        $stAttributes = $this->attributeString();
        if ( $stAttributes !== '' ) {
            yield "<{$this->stElement}{$stAttributes}>";
        } else {
            yield "<{$this->stElement}>";
        }
        $x = $this->innerBody();
        if ( is_string( $x ) ) {
            yield $x;
        } else {
            yield from $x;
        }
        yield "</{$this->stElement}>";
    }


    public function setElement( string $i_stElement ) : void {
        $this->stElement = $i_stElement;
    }


    /** @return iterable<string|Stringable>|string|Stringable */
    abstract protected function innerBody() : iterable|string|Stringable;


}
