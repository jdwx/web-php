<?php


declare( strict_types = 1 );


namespace JDWX\Web\Panels;


trait PanelContainerTrait {


    use YieldTrait;


    /** @var list<PanelInterface> */
    private array $rPanels = [];


    public function appendPanel( PanelInterface $i_panel ) : void {
        $this->rPanels[] = $i_panel;
    }


    public function prependPanel( PanelInterface $i_panel ) : void {
        array_unshift( $this->rPanels, $i_panel );
    }


    /** @param list<PanelInterface> $i_rPanels */
    public function setPanels( array $i_rPanels ) : void {
        $this->rPanels = $i_rPanels;
    }


    /** @return iterable<iterable<string>|string> */
    protected function _body() : iterable {
        foreach ( $this->rPanels as $panel ) {
            yield from $this->yield( $panel->body() );
        }
    }


    /** @return iterable<iterable<string>|string> */
    protected function _bodyEarly() : iterable {
        foreach ( $this->rPanels as $panel ) {
            yield from $this->yield( $panel->bodyEarly() );
        }
    }


    /** @return iterable<iterable<string>|string> */
    protected function _bodyLate() : iterable {
        foreach ( $this->rPanels as $panel ) {
            yield from $this->yield( $panel->bodyLate() );
        }
    }


    /** @return iterable<CssInterface> */
    protected function _cssList() : iterable {
        foreach ( $this->rPanels as $panel ) {
            yield from $panel->cssList();
        }
    }


    protected function _first() : void {
        foreach ( $this->rPanels as $panel ) {
            $panel->first();
        }
    }


    /** @return iterable<string> */
    protected function _head() : iterable {
        foreach ( $this->rPanels as $panel ) {
            yield from $this->yield( $panel->head() );
        }
    }


    /** @return iterable<string> */
    protected function _headerList() : iterable {
        foreach ( $this->rPanels as $panel ) {
            foreach ( $panel->headerList() as $header ) {
                yield $header;
            }
        }
    }


    protected function _last() : void {
        foreach ( $this->rPanels as $panel ) {
            $panel->last();
        }
    }


    /** @return iterable<string> */
    protected function _scripts() : iterable {
        $rScripts = [];
        foreach ( $this->rPanels as $panel ) {
            foreach ( $panel->scriptList() as $script ) {
                $stScript = strval( $script );
                if ( isset( $rScripts[ $stScript ] ) ) {
                    continue;
                }
                $rScripts[ $stScript ] = true;
                yield $stScript;
            }
        }
    }


}