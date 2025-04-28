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
abstract class HtmlPage extends AbstractHtmlPage {


    /** @var list<string> */
    private array $rstCSSUris = [];


    public function addCSSUri( string $i_stCSSFile ) : static {
        $this->rstCSSUris[] = $i_stCSSFile;
        return $this;
    }


    /** @return iterable<string> */
    protected function body() : iterable {
        yield from $this->yield( $this->content() );
    }


    /** @return string|iterable<string> */
    abstract protected function content() : string|iterable;


    /** @return list<string> */
    protected function cssUris() : array {
        return $this->rstCSSUris;
    }


}