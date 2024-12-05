<?php /** @noinspection PhpUnused */


declare( strict_types = 1 );


namespace JDWX\Web;


class SimpleHtmlPage extends HtmlPage {


    private ?string $nstContent = null;


    public function addContent( string $i_stContent ) : static {
        $this->nstContent = ( $this->nstContent ?? '' ) . $i_stContent;
        return $this;
    }


    public function setContent( string $i_stContent ) : static {
        $this->nstContent = $i_stContent;
        return $this;
    }


    protected function content() : string {
        return $this->nstContent ?? '';
    }


}