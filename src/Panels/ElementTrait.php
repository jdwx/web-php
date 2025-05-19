<?php


declare( strict_types = 1 );


namespace JDWX\Web\Panels;


use JDWX\Web\Stream\StringableStreamTrait;
use Stringable;


trait ElementTrait {


    use AttributeTrait;
    use StringableStreamTrait;


    private string $stTagName;

    private bool $bAlwaysClose = true;


    public function getAlwaysClose() : bool {
        return $this->bAlwaysClose;
    }


    public function getTagName() : string {
        return $this->stTagName;
    }


    public function setAlwaysClose( bool $i_bAlwaysClose ) : void {
        $this->bAlwaysClose = $i_bAlwaysClose;
    }


    public function setTagName( string $i_stTagName ) : void {
        $this->stTagName = $i_stTagName;
    }


    /** @return iterable<string|Stringable> */
    public function stream() : iterable {
        assert( strval( null ) === '' );
        yield '<' . $this->getTagName() . $this->attributeString() . '>';

        $inner = $this->inner();
        $bAny = false;
        /** @phpstan-ignore-next-line */
        if ( is_iterable( $inner ) ) {
            foreach ( $inner as $child ) {
                yield $child;
                $bAny = true;
            }
        } else {
            $inner = strval( $inner );
            if ( '' !== $inner ) {
                yield $inner;
                $bAny = true;
            }
        }
        if ( $this->bAlwaysClose || $bAny ) {
            yield '</' . $this->stTagName . '>';
        }
    }


    /** @return iterable<string|Stringable>|string|Stringable|null */
    protected function inner() : iterable|string|Stringable|null {
        return null;
    }


}