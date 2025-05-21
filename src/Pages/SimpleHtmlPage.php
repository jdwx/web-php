<?php /** @noinspection PhpUnused */


declare( strict_types = 1 );


namespace JDWX\Web\Pages;


class SimpleHtmlPage extends AbstractHtmlPage {


    use HtmlHeadTrait;
    use HtmlPageTrait;


    /** @var list<string> */
    private array $rCssUris = [];


    public function __construct( private ?string $nstContent = null, ?string $i_nstLanguage = null,
                                 ?string         $i_nstCharset = null ) {
        parent::__construct( i_nstCharset: $i_nstCharset );
        $this->setLanguage( $i_nstLanguage );
    }


    public function addContent( string $i_stContent ) : static {
        $this->nstContent = ( $this->nstContent ?? '' ) . $i_stContent;
        return $this;
    }


    public function addCssUri( string $i_stUri ) : static {
        $this->rCssUris[] = "<link rel=\"stylesheet\" href=\"{$i_stUri}\">";
        return $this;
    }


    /** @return iterable<string> */
    public function cssList() : iterable {
        return $this->rCssUris;
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


    /** @return iterable<string> */
    protected function body() : iterable {
        $nst = $this->prefix();
        if ( is_string( $nst ) ) {
            yield $nst;
        }
        if ( is_string( $this->nstContent ) ) {
            yield $this->nstContent;
        }
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