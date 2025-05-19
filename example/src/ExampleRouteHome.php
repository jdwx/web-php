<?php


declare( strict_types = 1 );


namespace JDWX\Web\Example;


use JDWX\Web\Framework\AbstractRoute;
use JDWX\Web\Framework\Response;
use JDWX\Web\Framework\ResponseInterface;
use JDWX\Web\Pages\SimpleHtmlPage;


class ExampleRouteHome extends AbstractRoute {


    protected function handleGET( string $i_stUri, string $i_stPath, array $i_rUriParameters ) : ?ResponseInterface {
        $page = new SimpleHtmlPage();
        $page->addCssUri( '/example.css' );
        $page->setTitle( 'Example Web Application' );
        $page->setContent(
            "<p>Hello, world!</p>\n"
            . "<p></p><input type=\"number\" id=\"num1\" value=\"0\"> + \n"
            . "<input type=\"number\" id=\"num2\" value=\"0\"> = \n"
            . "<span id=\"sum\">???</span></p>\n"
            . "<button id=\"add\">Add</button>\n"
            . "<script src=\"/example.js\"></script>\n"
            . "</body>\n"
        );
        return Response::page( $page );
    }


}
