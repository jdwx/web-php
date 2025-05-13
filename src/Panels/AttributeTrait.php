<?php


declare( strict_types = 1 );


namespace JDWX\Web\Panels;


trait AttributeTrait {


    /** @var array<string, true|string> */
    private array $rAttributes = [];


    /** @suppress PhanTypeMismatchReturn */
    public function addAttribute( string $i_stName, string $i_stValue ) : static {
        if ( empty( $this->rAttributes[ $i_stName ] ) || true === $this->rAttributes[ $i_stName ] ) {
            $this->rAttributes[ $i_stName ] = $i_stValue;
            return $this;
        }
        $this->rAttributes[ $i_stName ] .= ' ' . $i_stValue;
        return $this;
    }


    public function attributeString() : string {
        ksort( $this->rAttributes );
        $st = '';
        foreach ( $this->attrs() as $stKey => $nstValue ) {
            if ( is_string( $nstValue ) ) {
                $st .= ' ' . $stKey . '="' . htmlspecialchars( $nstValue, ENT_QUOTES ) . '"';
            } else {
                $st .= ' ' . $stKey;
            }
        }
        return $st;
    }


    /** @return iterable<string, true|string> */
    public function attrs() : iterable {
        foreach ( $this->rAttributes as $stKey => $bstValue ) {
            yield $stKey => $bstValue;
        }
    }


    public function getAttribute( string $i_stName ) : true|string|null {
        return $this->rAttributes[ $i_stName ] ?? null;
    }


    public function getAttributeEx( string $i_stName ) : true|string {
        $xValue = $this->getAttribute( $i_stName );
        if ( true === $xValue || is_string( $xValue ) ) {
            return $xValue;
        }
        throw new \InvalidArgumentException( 'Attribute "' . $i_stName . '" not set' );
    }


    public function hasAttribute( string $i_stName ) : bool {
        return isset( $this->rAttributes[ $i_stName ] );
    }


    /** @suppress PhanTypeMismatchReturn */
    public function removeAttribute( string $i_stName, ?string $i_nstValue = null ) : static {
        if ( ! is_string( $i_nstValue ) ) {
            unset( $this->rAttributes[ $i_stName ] );
            return $this;
        }
        if ( empty( $this->rAttributes[ $i_stName ] ) ) {
            return $this;
        }
        $stValue = $this->rAttributes[ $i_stName ];
        /** @phpstan-ignore-next-line */
        assert( is_string( $stValue ) );
        $rValue = preg_split( '/\s+/', trim( $stValue ) );
        $rValue = array_diff( $rValue, [ $i_nstValue ] );
        if ( empty( $rValue ) ) {
            unset( $this->rAttributes[ $i_stName ] );
            return $this;
        }
        $this->rAttributes[ $i_stName ] = implode( ' ', $rValue );
        return $this;
    }


    /** @suppress PhanTypeMismatchReturn */
    public function setAttribute( string $i_stName, bool|string $i_value = true ) : static {
        if ( false === $i_value ) {
            $this->removeAttribute( $i_stName );
            return $this;
        }
        $this->rAttributes[ $i_stName ] = $i_value;
        return $this;
    }


}