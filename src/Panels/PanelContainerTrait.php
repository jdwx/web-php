<?php


declare( strict_types = 1 );


namespace JDWX\Web\Panels;


use Stringable;


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


    /** @param iterable<PanelInterface> $i_rPanels */
    public function setPanels( iterable $i_rPanels ) : void {
        $this->rPanels = iterator_to_array( $i_rPanels, false );
    }


    /** @return iterable<iterable<string|Stringable>|string|Stringable> */
    protected function _body() : iterable {
        foreach ( $this->rPanels as $panel ) {
            yield from $this->yield( $panel->body() );
        }
    }


    /** @return iterable<iterable<string|Stringable>|string|Stringable> */
    protected function _bodyEarly() : iterable {
        foreach ( $this->rPanels as $panel ) {
            yield from $this->yield( $panel->bodyEarly() );
        }
    }


    /** @return iterable<iterable<string|Stringable>|string|Stringable> */
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


    /** @return iterable<string|Stringable> */
    protected function _head() : iterable {
        foreach ( $this->rPanels as $panel ) {
            yield from $this->yield( $panel->head() );
        }
    }


    /** @return iterable<string|Stringable> */
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


    /** @return iterable<ScriptInterface> */
    protected function _scriptList() : iterable {
        foreach ( $this->rPanels as $panel ) {
            yield from $panel->scriptList();
        }
    }


}