<?php


declare( strict_types = 1 );


namespace JDWX\Web\Tests;


use JDWX\Web\Backends\MockFilesBackend;
use JDWX\Web\FilesHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RuntimeException;


#[CoversClass( FilesHandler::class )]
final class FilesHandlerTest extends TestCase {


    public function testError() : void {
        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_OK,
            'name' => 'foo.txt',
        ] ];
        $fh = new FilesHandler( $rFiles );
        self::assertSame( UPLOAD_ERR_OK, $fh->error( 'foo' ) );
    }


    public function testErrorString() : void {
        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_NO_FILE,
            'name' => 'foo.txt',
        ] ];
        $fh = new FilesHandler( $rFiles );
        self::assertSame( 'UPLOAD_ERR_NO_FILE', $fh->errorString( 'foo' ) );
    }


    public function testErrorStringForCantWrite() : void {
        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_CANT_WRITE,
            'name' => 'foo.txt',
        ] ];
        $fh = new FilesHandler( $rFiles );
        self::assertSame( 'UPLOAD_ERR_CANT_WRITE', $fh->errorString( 'foo' ) );
    }


    public function testErrorStringForExtension() : void {
        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_EXTENSION,
            'name' => 'foo.txt',
        ] ];
        $fh = new FilesHandler( $rFiles );
        self::assertSame( 'UPLOAD_ERR_EXTENSION', $fh->errorString( 'foo' ) );
    }


    public function testErrorStringForNoTmp() : void {
        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_NO_TMP_DIR,
            'name' => 'foo.txt',
        ] ];
        $fh = new FilesHandler( $rFiles );
        self::assertSame( 'UPLOAD_ERR_NO_TMP_DIR', $fh->errorString( 'foo' ) );
    }


    public function testFetchAsString() : void {
        $stContent = 'test-content';
        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_OK,
            'name' => 'foo.txt',
            'size' => strlen( $stContent ),
            'tmp_name' => '/tmp/foo.txt',
        ], 'bar' => [
            'error' => UPLOAD_ERR_OK,
            'name' => 'bar.txt',
            'size' => strlen( $stContent ),
            'tmp_name' => '/tmp/bar.txt',
        ] ];
        $be = new MockFilesBackend();
        $be->addUploadedFile( '/tmp/foo.txt', $stContent );
        $be->addUploadedFile( '/tmp/bar.txt', $stContent );
        $fh = new FilesHandler( $rFiles, $be );
        self::assertSame( $stContent, $fh->fetchAsString( 'foo' ) );

    }


    public function testFetchAsStringForBogusTmpName() : void {
        $rFiles = [ 'baz' => [
            'error' => UPLOAD_ERR_OK,
            'name' => 'baz.txt',
            'size' => 10,
            'tmp_name' => '/tmp/baz.txt',
        ] ];
        $be = new MockFilesBackend();
        $fh = new FilesHandler( $rFiles, $be );
        $this->expectException( RuntimeException::class );
        $fh->fetchAsString( 'baz' );
    }


    public function testFetchAsStringForReadFailure() : void {
        $stContent = 'test-content';
        $be = new MockFilesBackend();
        $be->addUploadedFile( '/tmp/foo.txt', $stContent );
        $be->bFailToReadFile = true;
        $fh = new FilesHandler( [
            'foo' => [
                'error' => UPLOAD_ERR_OK,
                'name' => 'foo.txt',
                'size' => strlen( $stContent ),
                'tmp_name' => '/tmp/foo.txt',
            ],
        ], $be );
        $this->expectException( RuntimeException::class );
        $fh->fetchAsString( 'foo' );
    }


    public function testHas() : void {

        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_OK,
            'name' => 'foo.txt',
        ] ];
        $fh = new FilesHandler( $rFiles );
        self::assertTrue( $fh->has( 'foo' ) );
        self::assertFalse( $fh->has( 'bar' ) );

        $rFiles = [ 'foo' => [
            'error' => [ UPLOAD_ERR_OK ],
            'name' => [ 'foo.txt' ],
        ], 'bar' => [
            'error' => UPLOAD_ERR_OK,
            'name' => 'bar.txt',
        ] ];
        $fh = new FilesHandler( $rFiles );
        self::assertFalse( $fh->has( 'foo' ) );
        self::assertTrue( $fh->has( 'foo', 0 ) );
        self::assertFalse( $fh->has( 'foo', 1 ) );
        self::assertFalse( $fh->has( 'bar', 0 ) );
    }


    public function testHasForNoFile() : void {
        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_NO_FILE,
            'name' => '',
            'full_path' => '',
            'type' => '',
            'tmp_name' => '',
            'size' => 0,
        ] ];
        $fh = new FilesHandler( $rFiles );
        self::assertFalse( $fh->has( 'foo' ) );
    }


    public function testMove() : void {
        $stContent = 'test-content';
        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_OK,
            'name' => 'foo.txt',
            'size' => strlen( $stContent ),
            'tmp_name' => '/tmp/foo.txt',
        ] ];
        $be = new MockFilesBackend();
        $be->addUploadedFile( '/tmp/foo.txt', $stContent );
        $fh = new FilesHandler( $rFiles, $be );
        $fh->move( 'foo', '/tmp/bar.txt' );
        self::assertSame( $stContent, $be->fileGetContentsEx( '/tmp/bar.txt' ) );
        $be->bFailToMoveUpload = true;
        $this->expectException( RuntimeException::class );
        $fh->move( 'foo', '/tmp/baz.txt' );
    }


    public function testName() : void {
        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_OK,
            'name' => 'foo.txt',
        ] ];
        $fh = new FilesHandler( $rFiles );
        self::assertSame( 'foo.txt', $fh->name( 'foo' ) );

        $rFiles = [ 'foo' => [
            'error' => [ UPLOAD_ERR_OK ],
            'name' => [ 'foo.txt' ],
        ] ];
        $fh = new FilesHandler( $rFiles );
        self::assertSame( 'foo.txt', $fh->name( 'foo', 0 ) );

        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_OK,
        ] ];
        $fh = new FilesHandler( $rFiles );
        $this->expectException( RuntimeException::class );
        $fh->name( 'foo' );
    }


    public function testSize() : void {
        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_OK,
            'name' => 'foo.txt',
            'size' => 123,
        ] ];
        $fh = new FilesHandler( $rFiles );
        self::assertSame( 123, $fh->size( 'foo' ) );
    }


    public function testTmpName() : void {
        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_OK,
            'name' => 'foo.txt',
            'tmp_name' => '/tmp/foo.txt',
        ] ];
        $fh = new FilesHandler( $rFiles );
        self::assertSame( '/tmp/foo.txt', $fh->tmpName( 'foo' ) );
    }


    public function testType() : void {
        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_OK,
            'name' => 'foo.txt',
            'type' => 'text/plain',
        ] ];
        $fh = new FilesHandler( $rFiles );
        self::assertSame( 'text/plain', $fh->type( 'foo' ) );
    }


    public function testTypeForNoFile() : void {
        $fh = new FilesHandler( [] );
        $this->expectException( RuntimeException::class );
        $fh->type( 'foo' );
    }


    public function testTypeForUnexpectedMultipleFiles() : void {
        $rFiles = [ 'foo' => [
            'error' => [ UPLOAD_ERR_OK ],
            'name' => [ 'foo.txt' ],
            'type' => [ 'text/plain' ],
        ] ];
        $fh = new FilesHandler( $rFiles );
        $this->expectException( RuntimeException::class );
        $fh->type( 'foo' );
    }


    public function testTypeForUnexpectedOneFile() : void {
        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_OK,
            'name' => 'foo.txt',
            'type' => 'text/plain',
        ] ];
        $fh = new FilesHandler( $rFiles );
        $this->expectException( RuntimeException::class );
        $fh->type( 'foo', 0 );
    }


    public function testValidate() : void {
        $stContent = 'test-content';
        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_OK,
            'name' => 'foo.txt',
            'size' => strlen( $stContent ),
            'tmp_name' => '/tmp/foo.txt',
        ], 'baz' => [
            'error' => UPLOAD_ERR_NO_FILE,
            'name' => '',
        ], 'qux' => [
            'error' => UPLOAD_ERR_OK,
            'name' => 'qux.txt',
            'size' => strlen( $stContent ),
            'tmp_name' => '/tmp/qux.txt',
        ], 'quux' => [
            'error' => UPLOAD_ERR_OK,
            'name' => 'quux.txt',
            'size' => strlen( $stContent ),
            'tmp_name' => '/tmp/quux.txt',
        ] ];
        $be = new MockFilesBackend();
        $be->addUploadedFile( '/tmp/foo.txt', $stContent );
        $be->addUploadedFile( '/tmp/quux.txt', $stContent );
        $fh = new FilesHandler( $rFiles, $be );
        self::assertTrue( $fh->validate( 'foo' ) );
        $fh->move( 'foo', '/tmp/foo-moved.txt' );
        self::assertTrue( $be->fileExists( '/tmp/foo-moved.txt' ) );

        # File does not exist at all.
        self::assertFalse( $fh->validate( 'bar' ) );

        # File upload error.
        self::assertFalse( $fh->validate( 'baz' ) );

        # Fake tmp_name.
        self::assertFalse( $fh->validate( 'qux' ) );

        # Temp file has gone missing.
        self::assertTrue( $fh->has( 'quux' ) );
        self::assertSame( UPLOAD_ERR_OK, $fh->error( 'quux' ) );
        self::assertSame( '/tmp/quux.txt', $fh->tmpName( 'quux' ) );
        self::assertTrue( $be->isUploadedFile( '/tmp/quux.txt' ) );
        $be->bFailFileExists = true;
        self::assertFalse( $fh->validate( 'quux' ) );

    }


}
