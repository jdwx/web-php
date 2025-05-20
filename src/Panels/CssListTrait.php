<?php


declare( strict_types = 1 );


namespace JDWX\Web\Panels;


trait CssListTrait {


    /** @var list<CssInterface> */
    private array $rCss = [];


    public function addCss( CssInterface $i_css ) : void {
        $this->rCss[] = $i_css;
    }


    public function addCssInline( string $i_stCss ) : void {
        $this->rCss[] = new CssInline( $i_stCss );
    }


    public function addCssUri( string $i_stCssUri ) : void {
        $this->rCss[] = new CssLink( $i_stCssUri );
    }


    /** @return iterable<CssInterface> */
    public function cssList() : iterable {
        return $this->rCss;
    }


}