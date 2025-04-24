<?php


declare( strict_types = 1 );


namespace Framework;


use JDWX\Web\Backends\MockServer;
use JDWX\Web\Framework\StaticShim;
use JDWX\Web\Http;
use JDWX\Web\Request;
use Shims\MyStaticShim;
use Shims\MyTestCase;


require_once __DIR__ . '/../Shims/MyStaticShim.php';
require_once __DIR__ . '/../Shims/MyTestCase.php';


/** @covers \JDWX\Web\Framework\StaticShim */
final class StaticShimTest extends MyTestCase {


    public function testAddStaticUriForAuthoritative() : void {
        $req = $this->newRequest( '/example.nope' );
        $shim = new MyStaticShim( __DIR__, i_req: $req );
        self::assertFalse( $shim->handleStatic() );
        $shim->addStaticUri( '/' );
        ob_start();
        $bResult = $shim->handleStatic();
        ob_end_clean();
        self::assertTrue( $bResult );
        self::assertSame( 404, Http::getResponseCode() );
    }


    public function testRunForBogus() : void {
        $req = $this->newRequest( '/no/such/file' );
        $shim = new StaticShim( __DIR__, i_req: $req );
        self::assertFalse( $shim->run() );
    }


    public function testRunForBogusAuthoritative() : void {
        $req = $this->newRequest( '/no/such/file' );
        $shim = new StaticShim( __DIR__, i_req: $req );
        $shim->addStaticUri( '/' );
        ob_start();
        $bResult = $shim->run();
        $st = ob_get_clean();
        self::assertTrue( $bResult );
        self::assertStringContainsString( '404', $st );
        self::assertStringContainsString( 'Not Found', $st );
    }


    public function testRunForDirectory() : void {
        $req = $this->newRequest( '/static' );
        $shim = new StaticShim( __DIR__ . '/../../example/', i_req: $req );
        self::assertFalse( $shim->run() );
    }


    public function testRunForDirectoryAuthoritative() : void {
        $req = $this->newRequest( '/static' );
        $shim = new StaticShim( __DIR__ . '/../../example/', i_req: $req );
        $shim->addStaticUri( '/' );
        ob_start();
        $bResult = $shim->run();
        $st = ob_get_clean();
        self::assertTrue( $bResult );
        self::assertSame( 403, Http::getResponseCode() );
        self::assertStringContainsString( '403', $st );
        self::assertStringContainsString( 'Access Denied', $st );
    }


    public function testRunForExcludedDirectory() : void {
        $req = $this->newRequest( '/exclude/exclude.txt' );
        $shim = new StaticShim( __DIR__ . '/../../example/static/', i_req: $req );
        self::assertTrue( $shim->run() );
        $shim->excludeStaticPath( '/exclude' );
        self::assertFalse( $shim->run() );
    }


    public function testRunForInferredDocumentRoot() : void {
        $req = $this->newRequest( '/example.txt', __DIR__ . '/../../example/static/' );
        $shim = new StaticShim( i_req: $req );
        ob_start();
        $bResult = $shim->run();
        $st = ob_get_clean();
        self::assertTrue( $bResult );
        self::assertStringContainsString( 'This is a test.', $st );
    }


    public function testRunForMappedUri() : void {
        $req = $this->newRequest( '/decoy/alias.txt' );
        $shim = new StaticShim( __DIR__ . '/../../example/', i_req: $req );
        $shim->addStaticMap( '/decoy/', __DIR__ . '/../../example/static/alias/' );
        ob_start();
        $bResult = $shim->run();
        $st = ob_get_clean();
        self::assertTrue( $bResult );
        self::assertStringContainsString( 'aliased example', $st );
    }


    public function testRunForMultiViews() : void {
        $req = $this->newRequest( '/example2' );
        $shim = new StaticShim( __DIR__ . '/../../example/static/', i_req: $req );
        ob_start();
        $bResult = $shim->run();
        $st = ob_get_clean();
        self::assertTrue( $bResult );
        self::assertStringContainsString( 'This is also a test.', $st );
    }


    public function testRunForNotFound() : void {
        $req = $this->newRequest( '/example.exe' );
        $shim = new StaticShim( __DIR__, i_req: $req );
        self::assertFalse( $shim->run() );
    }


    public function testRunForNotFoundAuthoritative() : void {
        $req = $this->newRequest( '/example.exe' );
        $shim = new StaticShim( __DIR__, i_req: $req );
        $shim->addStaticUri( '/' );
        ob_start();
        $bResult = $shim->run();
        $st = ob_get_clean();
        self::assertTrue( $bResult );
        self::assertStringContainsString( '404', $st );
        self::assertStringContainsString( 'Not Found', $st );
    }


    public function testRunForOutsideStatic() : void {
        $req = $this->newRequest( '/exclude/exclude.txt' );
        $shim = new StaticShim( __DIR__ . '/../example/static/', i_req: $req );
        $shim->addStaticUri( '/alias/' );
        self::assertFalse( $shim->run() );
    }


    public function testRunForPHP() : void {
        $req = $this->newRequest( '/example.php' );
        $shim = new StaticShim( __DIR__ . '/../../example/static/', i_req: $req );
        self::assertFalse( $shim->run() );
    }


    public function testRunForSuccess() : void {
        $req = $this->newRequest( '/example.txt' );
        $shim = new StaticShim( __DIR__ . '/../../example/static/', i_req: $req );
        ob_start();
        $bResult = $shim->run();
        $st = ob_get_clean();
        self::assertTrue( $bResult );
        self::assertStringContainsString( 'This is a test.', $st );
    }


    public function testRunForUnknownType() : void {
        $req = $this->newRequest( '/example3.wtf' );
        $shim = new StaticShim( __DIR__ . '/../../example/static/', i_req: $req );
        ob_start();
        $bResult = $shim->run();
        $st = ob_get_clean();
        self::assertTrue( $bResult );
        self::assertSame( 200, Http::getResponseCode() );
        self::assertStringContainsString( 'text file', $st );
    }


    private function newRequest( string $i_stUri, ?string $i_nstDocumentRoot = null ) : Request {
        $srv = new MockServer();
        $srv = $srv->withRequestUri( $i_stUri );
        if ( is_string( $i_nstDocumentRoot ) ) {
            $srv = $srv->withDocumentRoot( $i_nstDocumentRoot );
        }
        return Request::synthetic( [], [], [], [], $srv );
    }


}
