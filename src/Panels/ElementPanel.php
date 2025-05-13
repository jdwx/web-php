<?php


declare( strict_types = 1 );


namespace JDWX\Web\Panels;


abstract class ElementPanel extends AbstractBodyPanel {


    use AttributeTrait;


    public function __construct( private string $stElement = 'div' ) {}


    /**
     * @inheritDoc
     */
    public function body() : iterable|string {
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


    /** @return iterable<string>|string */
    abstract protected function innerBody() : iterable|string;


}
