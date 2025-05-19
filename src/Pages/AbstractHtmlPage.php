<?php


declare( strict_types = 1 );


namespace JDWX\Web\Pages;


abstract class AbstractHtmlPage extends AbstractPage {


    public const ?string DEFAULT_CHARSET = 'UTF-8';


    public function __construct( ?string $i_nstContentType = null, ?string $i_nstCharset = null ) {
        parent::__construct( $i_nstContentType ?? 'text/html',
            $i_nstCharset ?? static::DEFAULT_CHARSET );
    }


}
