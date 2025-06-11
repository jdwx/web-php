<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests\Framework;


use JDWX\Strict\OK;
use JDWX\Web\Framework\Exceptions\NotFoundException;
use JDWX\Web\Framework\HttpError;
use JDWX\Web\Tests\Shims\MyTestCase;
use PHPUnit\Framework\Attributes\CoversClass;


require_once __DIR__ . '/../Shims/MyTestCase.php';


#[CoversClass( HttpError::class )]
final class HttpErrorTest extends MyTestCase {


    public function testCustomError() : void {
        $error = new HttpError( __DIR__ . '/../../example/errors/error%d.php' );
        $st = $error->render( 404 );
        self::assertStringContainsString( 'This is an example 404 error page.', $st );
    }


    public function testCustomErrorNotFound() : void {
        $error = new HttpError( __DIR__ . '/../example/errors/error%d.php' );
        $st = $error->render( 12345 );
        self::assertStringContainsString( 'Unknown Error', $st );
    }


    public function testErrorName() : void {
        $error = new HttpError();
        self::assertSame( 'Not Found', $error->errorName( 404 ) );
        self::assertSame( 'TEST_ERROR', $error->errorName( 404, 'TEST_ERROR' ) );
    }


    public function testErrorText() : void {
        $error = new HttpError();
        self::assertSame( 'The file or resource you requested was not found.',
            $error->errorText( 404 ) );
        self::assertSame( 'TEST_TEXT', $error->errorText( 404, 'TEST_TEXT' ) );
        self::assertSame( '', $error->errorText( 12345 ) );
    }


    public function testShow() : void {
        $error = new HttpError();
        OK::ob_start();
        $error->show( 404 );
        $result = OK::ob_get_clean();
        self::assertStringContainsString( 'Not Found', $result );
    }


    public function testShowException() : void {
        $error = new HttpError();
        OK::ob_start();
        $error->showException( new NotFoundException( i_nstDisplay: 'TEST_EXCEPTION' ) );
        $result = OK::ob_get_clean();
        self::assertStringContainsString( 'TEST_EXCEPTION', $result );
    }


}
