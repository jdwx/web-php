<?php /** @noinspection PhpUnused */


declare( strict_types = 1 );


namespace JDWX\Web;


class SimpleHtmlPage extends HtmlPage {


    public function __construct( private ?string $nstContent = null, ?string $i_nstLanguage = null ) {
        parent::__construct( $i_nstLanguage );
    }


    public function addContent( string $i_stContent ) : static {
        $this->nstContent = ( $this->nstContent ?? '' ) . $i_stContent;
        return $this;
    }


    public function getContent() : ?string {
        return $this->nstContent;
    }


    public function prependContent( string $i_stContent ) : static {
        $this->nstContent = $i_stContent . ( $this->nstContent ?? '' );
        return $this;
    }


    public function setContent( string $i_stContent ) : static {
        $this->nstContent = $i_stContent;
        return $this;
    }


    /** @return string|iterable<string> */
    protected function content() : string|iterable {
        $nst = $this->prefix();
        if ( is_string( $nst ) ) {
            yield $nst;
        }
        yield $this->nstContent ?? '';
        $nst = $this->suffix();
        if ( is_string( $nst ) ) {
            yield $nst;
        }
    }


    protected function prefix() : ?string {
        return null;
    }


    protected function suffix() : ?string {
        return null;
    }


}