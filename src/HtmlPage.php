<?php /** @noinspection PhpUnused */


declare( strict_types = 1 );


namespace JDWX\Web;


/**
 *  Class HtmlPage
 *
 * This class is a base class for generating very simple HTML pages.
 * It's designed to be used for simple server-generated pages like
 * error pages.
 */
abstract class HtmlPage extends AbstractPage {


    /** @var list<string> */
    private array $rstCSSFiles = [];

    private ?string $nstTitle = null;

    private ?string $nstCharset = 'UTF-8';

    private string $stLanguage = 'en';


    public function __construct( ?string $i_nstLanguage = null ) {
        parent::__construct( 'text/html' );
        if ( is_string( $i_nstLanguage ) ) {
            $this->stLanguage = $i_nstLanguage;
        }
    }


    public function addCSS( string $i_stCSSFile ) : static {
        $this->rstCSSFiles[] = $i_stCSSFile;
        return $this;
    }


    public function getCharset() : ?string {
        return $this->nstCharset;
    }


    public function getDefaultLanguage() : string {
        return $this->stLanguage;
    }


    public function getTitle() : ?string {
        return $this->nstTitle;
    }


    /** @return list<string> */
    public function listCSS() : array {
        return $this->rstCSSFiles;
    }


    public function setCharset( ?string $i_nstCharset ) : static {
        $this->nstCharset = $i_nstCharset;
        return $this;
    }


    public function setDefaultLanguage( string $i_stLanguage ) : static {
        $this->stLanguage = $i_stLanguage;
        return $this;
    }


    public function setTitle( string $i_stTitle ) : static {
        $this->nstTitle = $i_stTitle;
        return $this;
    }


    /** @return \Generator<string> */
    public function stream() : \Generator {
        yield $this->docType();
        yield $this->html();
        yield $this->head();
        # Don't do "yield from" because it messes up the keys.
        foreach ( $this->body() as $stChunk ) {
            yield $stChunk;
        }
        yield '</html>';
    }


    /** @return \Generator<string> */
    protected function body() : \Generator {
        yield '<body>';
        $x = $this->content();
        if ( is_string( $x ) ) {
            yield $x;
        } else {
            # Don't do "yield from" because it messes up the keys.
            foreach ( $x as $stChunk ) {
                yield $stChunk;
            }
        }
        yield '</body>';
    }


    protected function charset() : string {
        if ( is_string( $this->nstCharset ) ) {
            return "<meta charset=\"{$this->nstCharset}\">";
        }
        return '';
    }


    /** @return string|iterable<string> */
    abstract protected function content() : string|iterable;


    protected function css() : string {
        $st = '';
        foreach ( $this->rstCSSFiles as $stCSSFile ) {
            $st .= "<link rel=\"stylesheet\" href=\"{$stCSSFile}\">";
        }
        return $st;
    }


    protected function docType() : string {
        return "<!DOCTYPE html>\n";
    }


    protected function head() : string {
        /** @noinspection HtmlRequiredTitleElement */
        $st = '<head>';
        $st .= $this->viewport();
        $st .= $this->title();
        $st .= $this->charset();
        $st .= $this->css();
        $st .= '</head>';
        return $st;
    }


    protected function html() : string {
        return "<html lang=\"{$this->stLanguage}\">\n";
    }


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