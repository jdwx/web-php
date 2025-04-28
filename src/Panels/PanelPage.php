<?php


declare( strict_types = 1 );


namespace JDWX\Web\Panels;


use JDWX\Web\AbstractHtmlPage;
use JDWX\Web\Http;


/**
 * A PanelPage is a page made up of one or more panels. It is designed to
 * allow constructing elements of a page separately without the need for
 * them to know about each other. It lets each panel specify various page
 * elements, like headers, scripts, and CSS URIs, and then combines them into
 * a single page that renders everything in the right order.
 */
class PanelPage extends AbstractHtmlPage {


    /** @param list<PanelInterface> $rPanels */
    public function __construct( private array $rPanels, ?string $i_nstLanguage = null ) {
        parent::__construct( $i_nstLanguage );
    }


    public function appendPanel( PanelInterface $i_panel ) : void {
        $this->rPanels[] = $i_panel;
    }


    public function prependPanel( PanelInterface $i_panel ) : void {
        array_unshift( $this->rPanels, $i_panel );
    }


    /** @return iterable<string> */
    protected function _scripts() : iterable {
        $rScripts = [];
        foreach ( $this->scripts() as $script ) {
            $stScript = strval( $script );
            if ( isset( $rScripts[ $stScript ] ) ) {
                continue;
            }
            $rScripts[ $stScript ] = true;
            yield $stScript;
        }
    }


    protected function body() : iterable {
        foreach ( $this->rPanels as $panel ) {
            yield from $this->yield( $panel->bodyEarly() );
        }
        foreach ( $this->rPanels as $panel ) {
            yield from $this->yield( $panel->body() );
        }
        foreach ( $this->rPanels as $panel ) {
            yield from $this->yield( $panel->bodyLate() );
        }
        yield from $this->_scripts();
    }


    /** @return iterable<string> */
    protected function cssUris() : iterable {
        $rCssUris = [];
        foreach ( $this->rPanels as $panel ) {
            foreach ( $panel->cssUris() as $stCssUri ) {
                $rCssUris[ $stCssUri ] = true;
            }
        }
        return array_keys( $rCssUris );
    }


    protected function first() : void {
        foreach ( $this->rPanels as $panel ) {
            $panel->first();
        }
    }


    protected function head() : iterable {
        yield from parent::head();
        foreach ( $this->rPanels as $panel ) {
            yield from $this->yield( $panel->head() );
        }
    }


    protected function headers() : void {
        parent::headers();
        foreach ( $this->rPanels as $panel ) {
            foreach ( $panel->headers() as $header ) {
                Http::setHeader( $header );
            }
        }
    }


    protected function last() : void {
        foreach ( $this->rPanels as $panel ) {
            $panel->last();
        }
    }


    /** @return iterable<ScriptInterface> */
    protected function scripts() : iterable {
        foreach ( $this->rPanels as $panel ) {
            yield from $panel->scripts();
        }
    }


}