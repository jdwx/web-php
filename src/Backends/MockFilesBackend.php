<?php


declare( strict_types = 1 );


namespace JDWX\Web\Backends;


/**
 * MockFilesBackend is used for unit testing. It is included as part of the module
 * because higher-level modules built on this also need to unit test without
 * reinventing the wheel.
 */
class MockFilesBackend extends AbstractFilesBackend {


    /** @var bool Set this to force moveUploadedFile() to fail. */
    public bool $bFailToMoveUpload = false;

    /** @var bool Set this to force fileGetContents() to fail. */
    public bool $bFailToReadFile = false;

    /** @var bool Set this to force fileExists() to return false. */
    public bool $bFailFileExists = false;

    /** @var array<string, string> */
    public array $rMovedFiles = [];

    /** @var array<string, string> */
    public array $rUploadedFiles = [];


    public function addUploadedFile( string $i_stTmpPath, string $i_stContent ) : void {
        $this->rUploadedFiles[ $i_stTmpPath ] = $i_stContent;
    }


    public function fileExists( string $i_stPath ) : bool {
        if ( $this->bFailFileExists ) {
            return false;
        }
        if ( isset( $this->rUploadedFiles[ $i_stPath ] ) ) {
            return true;
        }
        if ( isset( $this->rMovedFiles[ $i_stPath ] ) ) {
            return true;
        }
        return false;
    }


    public function fileGetContents( string $i_stPath ) : string|false {
        if ( $this->bFailToReadFile ) {
            return false;
        }
        if ( isset( $this->rUploadedFiles[ $i_stPath ] ) ) {
            $x = $this->rUploadedFiles[ $i_stPath ];
        } elseif ( isset( $this->rMovedFiles[ $i_stPath ] ) ) {
            $x = $this->rMovedFiles[ $i_stPath ];
        } else {
            return false;
        }
        return $x;
    }


    public function isUploadedFile( string $i_stPath ) : bool {
        return array_key_exists( $i_stPath, $this->rUploadedFiles );
    }


    public function moveUploadedFile( string $i_stFrom, string $i_stTo ) : bool {
        if ( $this->bFailToMoveUpload ) {
            return false;
        }
        if ( ! isset( $this->rUploadedFiles[ $i_stFrom ] ) ) {
            return false;
        }
        $stFrom = $this->rUploadedFiles[ $i_stFrom ];
        $this->rMovedFiles[ $i_stTo ] = $stFrom;
        unset( $this->rUploadedFiles[ $i_stFrom ] );
        return true;
    }


    public function removeFile( string $i_stPath ) : void {
        if ( array_key_exists( $i_stPath, $this->rUploadedFiles ) ) {
            unset( $this->rUploadedFiles[ $i_stPath ] );
        }
        if ( array_key_exists( $i_stPath, $this->rMovedFiles ) ) {
            unset( $this->rMovedFiles[ $i_stPath ] );
        }
    }


}
