<?php


declare( strict_types = 1 );


namespace JDWX\Web\Pages;


use Stringable;


/**
 * Provides a simple implementation of the HTML <head> block with the
 * most common options. (E.g., title, CSS links, <meta> tags.)
 */
trait HtmlHeadTrait {


    private ?string $nstTitle = null;


    abstract public function getCharset() : ?string;


    public function getTitle() : ?string {
        return $this->nstTitle;
    }


    abstract public function hasCharset() : bool;


    public function setTitle( string $i_stTitle ) : void {
        $this->nstTitle = $i_stTitle;
    }


    protected function charset() : string {
        if ( $this->hasCharset() ) {
            return '<meta charset="' . $this->getCharset() . '">';
        }
        return '';
    }


    protected function css() : string {
        $st = '';
        $r = [];
        foreach ( $this->cssList() as $css ) {
            $stCss = strval( $css );
            if ( isset( $r[ $stCss ] ) ) {
                continue;
            }
            $r[ $stCss ] = true;
            $st .= $stCss;
        }
        return $st;
    }


    /** @return iterable<string|Stringable> */
    abstract protected function cssList() : iterable;


    /** @return iterable<string> */
    protected function head() : iterable {
        $st = $this->viewport();
        $st .= $this->title();
        $st .= $this->charset();
        $st .= $this->css();
        yield $st;
    }


    protected function title() : string {
        $nstTitle = $this->getTitle();
        if ( is_string( $nstTitle ) ) {
            return "<title>{$nstTitle}</title>";
        }
        return '';
    }


    protected function viewport() : string {
        return '<meta content="width=device-width, initial-scale=1.0" name="viewport">';
    }


}