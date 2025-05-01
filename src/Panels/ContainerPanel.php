<?php


declare( strict_types = 1 );


namespace JDWX\Web\Panels;


class ContainerPanel implements PanelInterface {


    /** @param iterable<PanelInterface>|null $i_panels */
    public function __construct( ?iterable $i_panels = null ) {
        if ( is_iterable( $i_panels ) ) {
            $this->setPanels( $i_panels );
        }
    }


    use PassThroughPanelTrait;
}
