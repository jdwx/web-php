<?php


declare( strict_types = 1 );


use JDWX\Web\Backends\MockFilesBackend;
use JDWX\Web\FilesHandler;
use PHPUnit\Framework\TestCase;


class FilesHandlerTest extends TestCase {


    public function testError() : void {
        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_OK,
            'name' => 'foo.txt',
        ] ];
        $fh = new FilesHandler( $rFiles );
        static::assertSame( UPLOAD_ERR_OK, $fh->error( 'foo' ) );
    }


    public function testErrorString() : void {
        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_NO_FILE,
            'name' => 'foo.txt',
        ] ];
        $fh = new FilesHandler( $rFiles );
        static::assertSame( 'UPLOAD_ERR_NO_FILE', $fh->errorString( 'foo' ) );
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
        static::assertSame( $stContent, $fh->fetchAsString( 'foo' ) );

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
        static::expectException( RuntimeException::class );
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
        static::expectException( RuntimeException::class );
        $fh->fetchAsString( 'foo' );
    }


    public function testHas() : void {

        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_OK,
            'name' => 'foo.txt',
        ] ];
        $fh = new FilesHandler( $rFiles );
        static::assertTrue( $fh->has( 'foo' ) );
        static::assertFalse( $fh->has( 'bar' ) );

        $rFiles = [ 'foo' => [
            'error' => [ UPLOAD_ERR_OK ],
            'name' => [ 'foo.txt' ],
        ], 'bar' => [
            'error' => UPLOAD_ERR_OK,
            'name' => 'bar.txt',
        ] ];
        $fh = new FilesHandler( $rFiles );
        static::assertFalse( $fh->has( 'foo' ) );
        static::assertTrue( $fh->has( 'foo', 0 ) );
        static::assertFalse( $fh->has( 'foo', 1 ) );
        static::assertFalse( $fh->has( 'bar', 0 ) );
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
        static::assertFalse( $fh->has( 'foo' ) );
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
        static::assertSame( $stContent, $be->fileGetContentsEx( '/tmp/bar.txt' ) );
        $be->bFailToMoveUpload = true;
        static::expectException( RuntimeException::class );
        $fh->move( 'foo', '/tmp/baz.txt' );
    }


    public function testName() : void {
        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_OK,
            'name' => 'foo.txt',
        ] ];
        $fh = new FilesHandler( $rFiles );
        static::assertSame( 'foo.txt', $fh->name( 'foo' ) );

        $rFiles = [ 'foo' => [
            'error' => [ UPLOAD_ERR_OK ],
            'name' => [ 'foo.txt' ],
        ] ];
        $fh = new FilesHandler( $rFiles );
        static::assertSame( 'foo.txt', $fh->name( 'foo', 0 ) );

        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_OK,
        ] ];
        $fh = new FilesHandler( $rFiles );
        static::expectException( RuntimeException::class );
        $fh->name( 'foo' );
    }


    public function testSize() : void {
        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_OK,
            'name' => 'foo.txt',
            'size' => 123,
        ] ];
        $fh = new FilesHandler( $rFiles );
        static::assertSame( 123, $fh->size( 'foo' ) );
    }


    public function testTmpName() : void {
        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_OK,
            'name' => 'foo.txt',
            'tmp_name' => '/tmp/foo.txt',
        ] ];
        $fh = new FilesHandler( $rFiles );
        static::assertSame( '/tmp/foo.txt', $fh->tmpName( 'foo' ) );
    }


    public function testType() : void {
        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_OK,
            'name' => 'foo.txt',
            'type' => 'text/plain',
        ] ];
        $fh = new FilesHandler( $rFiles );
        static::assertSame( 'text/plain', $fh->type( 'foo' ) );
    }


    public function testTypeForNoFile() : void {
        $fh = new FilesHandler( [] );
        static::expectException( RuntimeException::class );
        $fh->type( 'foo' );
    }


    public function testTypeForUnexpectedMultipleFiles() : void {
        $rFiles = [ 'foo' => [
            'error' => [ UPLOAD_ERR_OK ],
            'name' => [ 'foo.txt' ],
            'type' => [ 'text/plain' ],
        ] ];
        $fh = new FilesHandler( $rFiles );
        static::expectException( RuntimeException::class );
        $fh->type( 'foo' );
    }


    public function testTypeForUnexpectedOneFile() : void {
        $rFiles = [ 'foo' => [
            'error' => UPLOAD_ERR_OK,
            'name' => 'foo.txt',
            'type' => 'text/plain',
        ] ];
        $fh = new FilesHandler( $rFiles );
        static::expectException( RuntimeException::class );
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
        static::assertTrue( $fh->validate( 'foo' ) );
        $fh->move( 'foo', '/tmp/foo-moved.txt' );
        static::assertTrue( $be->fileExists( '/tmp/foo-moved.txt' ) );

        # File does not exist at all.
        static::assertFalse( $fh->validate( 'bar' ) );

        # File upload error.
        static::assertFalse( $fh->validate( 'baz' ) );

        # Fake tmp_name.
        static::assertFalse( $fh->validate( 'qux' ) );

        # Temp file has gone missing.
        static::assertTrue( $fh->has( 'quux' ) );
        static::assertSame( UPLOAD_ERR_OK, $fh->error( 'quux' ) );
        static::assertSame( '/tmp/quux.txt', $fh->tmpName( 'quux' ) );
        static::assertTrue( $be->isUploadedFile( '/tmp/quux.txt' ) );
        $be->bFailFileExists = true;
        static::assertFalse( $fh->validate( 'quux' ) );

    }


}
