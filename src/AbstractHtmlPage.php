<?php


declare( strict_types = 1 );


namespace JDWX\Web;


use JDWX\Web\Panels\CssInterface;


abstract class AbstractHtmlPage extends AbstractPage {


    private ?string $nstTitle = null;

    private ?string $nstCharset = 'UTF-8';

    private string $stLanguage = 'en';


    public function __construct( ?string $i_nstLanguage = null ) {
        parent::__construct( 'text/html' );
        if ( is_string( $i_nstLanguage ) ) {
            $this->stLanguage = $i_nstLanguage;
        }
    }


    public function getCharset() : ?string {
        return $this->nstCharset;
    }


    public function getLanguage() : string {
        return $this->stLanguage;
    }


    public function getTitle() : ?string {
        return $this->nstTitle;
    }


    public function hasCharset() : bool {
        return ! empty( $this->nstCharset );
    }


    public function setCharset( ?string $i_nstCharset ) : static {
        $this->nstCharset = $i_nstCharset;
        return $this;
    }


    public function setLanguage( string $i_stLanguage ) : static {
        $this->stLanguage = $i_stLanguage;
        return $this;
    }


    public function setTitle( string $i_stTitle ) : static {
        $this->nstTitle = $i_stTitle;
        return $this;
    }


    /** @return iterable<string> */
    public function stream() : iterable {
        $this->first();
        $this->headers();
        yield $this->docType();
        yield $this->html();
        # Don't do "yield from" because it messes up the keys.
        yield '<head>';
        foreach ( $this->head() as $stChunk ) {
            yield $stChunk;
        }
        yield '</head>';
        # Don't do "yield from" because it messes up the keys.
        yield '<body>';
        foreach ( $this->body() as $stChunk ) {
            yield $stChunk;
        }
        yield '</body></html>';
        $this->last();
    }


    /** @return iterable<string> */
    abstract protected function body() : iterable;


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


    /** @return iterable<CssInterface> */
    abstract protected function cssList() : iterable;


    protected function docType() : string {
        return "<!DOCTYPE html>\n";
    }


    protected function first() : void {}


    /** @return iterable<string> */
    protected function head() : iterable {
        $st = $this->viewport();
        $st .= $this->title();
        $st .= $this->charset();
        $st .= $this->css();
        yield $st;
    }


    /** @return iterable<string> */
    abstract protected function headerList() : iterable;


    protected function headers() : void {
        Http::setHeaders( $this->headerList() );
    }


    protected function html() : string {
        return '<html lang="' . $this->getLanguage() . "\">\n";
    }


    protected function last() : void {}


    protected function title() : string {
        if ( is_string( $this->nstTitle ) ) {
            return "<title>{$this->nstTitle}</title>";
        }
        return '';
    }


    protected function viewport() : string {
        return '<meta name="viewport" content="width=device-width, initial-scale=1">';
    }


}