<?php


declare( strict_types = 1 );


namespace JDWX\Web\Pages;


use Stringable;


/**
 * This provides the very basic structure that an HTML page
 * can't live without:
 *
 * <!DOCTYPE html>
 * <html>
 *     <head>...</head>
 *     <body>...</body>
 * </html>
 *
 * Allowing the user to fill in the HTML head and body as needed.
 */
trait HtmlPageTrait {


    private string $stLanguage = 'en';


    public function getLanguage() : string {
        return $this->stLanguage;
    }


    public function setLanguage( ?string $i_nstLanguage ) : void {
        if ( is_string( $i_nstLanguage ) ) {
            $this->stLanguage = $i_nstLanguage;
        }
    }


    /** @return iterable<string> */
    public function stream() : iterable {
        $this->first();
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


    /** @return iterable<string|Stringable> */
    abstract protected function body() : iterable;


    protected function docType() : string {
        return "<!DOCTYPE html>\n";
    }


    protected function first() : void {}


    /** @return iterable<string|Stringable> */
    abstract protected function head() : iterable;


    protected function html() : string {
        return '<html lang="' . $this->getLanguage() . "\">\n";
    }


    protected function last() : void {}


}