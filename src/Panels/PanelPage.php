<?php


declare( strict_types = 1 );


namespace JDWX\Web\Panels;


use JDWX\Web\AbstractHtmlPage;


/**
 * A PanelPage is a page made up of one or more panels. It is designed to
 * allow constructing elements of a page separately without the need for
 * them to know about each other. It lets each panel specify various page
 * elements, like headers, scripts, and CSS URIs, and then combines them into
 * a single page that renders everything in the right order.
 */
class PanelPage extends AbstractHtmlPage {


    use PanelContainerTrait;


    /** @param list<PanelInterface>|PanelInterface|null $i_nrPanels */
    public function __construct( array|PanelInterface|null $i_nrPanels = null, ?string $i_nstLanguage = null ) {
        if ( $i_nrPanels instanceof PanelInterface ) {
            $i_nrPanels = [ $i_nrPanels ];
        }
        if ( is_array( $i_nrPanels ) ) {
            $this->setPanels( $i_nrPanels );
        }
        parent::__construct( $i_nstLanguage );
    }


    protected function body() : iterable {
        yield from $this->_bodyEarly();
        yield from $this->_body();
        yield from $this->_bodyLate();
        yield from $this->scripts();
    }


    protected function cssList() : iterable {
        yield from $this->_cssList();
    }


    protected function first() : void {
        $this->_first();
    }


    protected function head() : iterable {
        yield from parent::head();
        yield from $this->_head();
    }


    /** @return iterable<string> */
    protected function headerList() : iterable {
        yield from $this->_headerList();
    }


    protected function last() : void {
        $this->_last();
    }


    /** @return iterable<string> */
    protected function scripts() : iterable {
        $rScripts = [];
        foreach ( $this->_scriptList() as $script ) {
            $stScript = strval( $script );
            if ( isset( $rScripts[ $stScript ] ) ) {
                continue;
            }
            $rScripts[ $stScript ] = true;
            yield $stScript;
        }
    }


}