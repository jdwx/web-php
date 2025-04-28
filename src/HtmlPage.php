<?php /** @noinspection PhpUnused */


declare( strict_types = 1 );


namespace JDWX\Web;


use JDWX\Web\Panels\CssListTrait;
use JDWX\Web\Panels\YieldTrait;


/**
 *  Class HtmlPage
 *
 * This class is a base class for generating very simple HTML pages.
 * It's designed to be used for simple server-generated pages like
 * error pages.
 */
abstract class HtmlPage extends AbstractHtmlPage {


    use CssListTrait;
    use YieldTrait;


    /** @return iterable<string> */
    protected function body() : iterable {
        yield from $this->yield( $this->content() );
    }


    /** @return string|iterable<string> */
    abstract protected function content() : string|iterable;


    /** @return iterable<string> */
    protected function headerList() : iterable {
        return [];
    }


}