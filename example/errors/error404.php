<?php


declare( strict_types = 1 );


( function () {

    $page = new \JDWX\Web\Pages\SimpleHtmlPage();
    $page->addCSSUri( '/example.css' );
    $page->setTitle( '404 Error' );
    $page->addContent( 'This is an example 404 error page.' );
    echo $page->render();

} )();