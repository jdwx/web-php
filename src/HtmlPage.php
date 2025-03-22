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
abstract class HtmlPage implements \Stringable {


    /** @var list<string> */
    private array $rstCSSFiles = [];

    private ?string $nstTitle = null;

    private ?string $nstCharset = 'UTF-8';


    public function __toString() : string {
        return $this->render();
    }


    public function addCSS( string $i_stCSSFile ) : static {
        $this->rstCSSFiles[] = $i_stCSSFile;
        return $this;
    }


    public function echo() : void {
        echo $this->render();
    }


    public function render( ?string $i_nstLanguage = null ) : string {
        return $this->docType() . $this->html( $i_nstLanguage ) . $this->head() . $this->body() . '</html>';
    }


    public function setCharset( ?string $i_nstCharset ) : static {
        $this->nstCharset = $i_nstCharset;
        return $this;
    }


    public function setTitle( string $i_stTitle ) : static {
        $this->nstTitle = $i_stTitle;
        return $this;
    }


    protected function body() : string {
        return '<body>' . $this->content() . '</body>';
    }


    abstract protected function content() : string;


    protected function docType() : string {
        return "<!DOCTYPE html>\n";
    }


    protected function head() : string {
        /** @noinspection HtmlRequiredTitleElement */
        $st = '<head>';
        $st .= $this->viewport();
        if ( is_string( $this->nstTitle ) ) {
            $st .= "<title>{$this->nstTitle}</title>";
        }
        if ( is_string( $this->nstCharset ) ) {
            $st .= "<meta charset=\"{$this->nstCharset}\">";
        }
        foreach ( $this->rstCSSFiles as $stCSSFile ) {
            $st .= "<link rel=\"stylesheet\" href=\"{$stCSSFile}\">";
        }
        $st .= '</head>';
        return $st;
    }


    protected function html( ?string $i_nstLanguage = null ) : string {
        return "<html lang=\"" . ( $i_nstLanguage ?? 'en' ) . "\">\n";
    }


    protected function viewport() : string {
        return '<meta name="viewport" content="width=device-width, initial-scale=1">';
    }


}