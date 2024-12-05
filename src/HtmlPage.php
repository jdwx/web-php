<?php /** @noinspection PhpUnused */


declare( strict_types = 1 );


namespace JDWX\Web;


abstract class HtmlPage {


    /** @var list<string> */
    private array $rstCSSFiles = [];

    private ?string $nstTitle = null;

    private ?string $nstCharset = 'UTF-8';


    public function addCSS( string $i_stCSSFile ) : static {
        $this->rstCSSFiles[] = $i_stCSSFile;
        return $this;
    }


    public function echo() : void {
        echo $this->render();
    }


    public function render() : string {
        return $this->docType() . $this->html() . $this->head() . $this->body() . '</html>';
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


    protected function html( string $i_stLang = 'en' ) : string {
        return "<html lang=\"{$i_stLang}\">\n";
    }


    protected function viewport() : string {
        return '<meta name="viewport" content="width=device-width, initial-scale=1">';
    }


}