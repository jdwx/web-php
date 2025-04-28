<?php


declare( strict_types = 1 );


namespace JDWX\Web\Panels;


trait PassThroughPanelTrait {


    use PanelContainerTrait {
        _body as public body;
        _bodyEarly as public bodyEarly;
        _bodyLate as public bodyLate;
        _head as public head;
        _headerList as public headerList;
        _scriptList as public scriptList;
        _cssList as public cssList;
        _first as public first;
        _last as public last;
    }
}