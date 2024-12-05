<?php


declare( strict_types = 1 );


namespace JDWX\Web;


use RuntimeException;


readonly class FilesHandler {


    private IFilesBackend $be;


    /** @param mixed[] $rFiles */
    public function __construct( private array $rFiles, ?IFilesBackend $i_be = null ) {
        if ( ! $i_be instanceof IFilesBackend ) {
            $i_be = new PHPFilesBackend();
        }
        $this->be = $i_be;
    }


    public function error( string $i_stTag, ?int $i_niIndex = null ) : int {
        $this->check( $i_stTag, $i_niIndex );
        return $this->get( $i_stTag, 'error', $i_niIndex );
    }


    public function errorString( string $i_stTag, ?int $i_niIndex = null ) : string {
        $this->check( $i_stTag, $i_niIndex );
        return match ( $this->error( $i_stTag, $i_niIndex ) ) {
            UPLOAD_ERR_OK => 'UPLOAD_ERR_OK',
            UPLOAD_ERR_INI_SIZE => 'UPLOAD_ERR_INI_SIZE',
            UPLOAD_ERR_FORM_SIZE => 'UPLOAD_ERR_FORM_SIZE',
            UPLOAD_ERR_PARTIAL => 'UPLOAD_ERR_PARTIAL',
            UPLOAD_ERR_NO_FILE => 'UPLOAD_ERR_NO_FILE',
            UPLOAD_ERR_NO_TMP_DIR => 'UPLOAD_ERR_NO_TMP_DIR',
            UPLOAD_ERR_CANT_WRITE => 'UPLOAD_ERR_CANT_WRITE',
            UPLOAD_ERR_EXTENSION => 'UPLOAD_ERR_EXTENSION',
            default => 'UPLOAD_UNKNOWN_ERROR',
        };
    }


    /**
     * @param string $i_stTag Tag of file.
     * @param int|null $i_niIndex Index of file (if applicable)
     * @return string The contents of the file.
     *
     * Retrieve the contents of the uploaded file as a string. This is intended to be
     * used before or in lieu of moving the file to a permanent location. After a move,
     * this won't work anymore.
     */
    public function fetchAsString( string $i_stTag, ?int $i_niIndex = null ) : string {
        $stPath = $this->tmpName( $i_stTag, $i_niIndex );
        if ( ! $this->be->isUploadedFile( $stPath ) ) {
            $stLabel = $this->label( $i_stTag, $i_niIndex );
            throw new RuntimeException( "File {$stLabel} is not an upload." );
        }
        return $this->be->fileGetContentsEx( $stPath );
    }


    /** @suppress PhanAccessReadOnlyProperty Phan bug; see issue 4834. */
    public function has( string $i_stTag, ?int $i_niIndex = null ) : bool {
        if ( ! $this->hasAtAll( $i_stTag, $i_niIndex ) ) {
            return false;
        }
        return $this->name( $i_stTag, $i_niIndex ) !== '';
    }


    public function move( string $i_stTag, string $i_stToPath, ?int $i_niIndex = null ) : void {
        $stFromPath = $this->tmpName( $i_stTag, $i_niIndex );
        $this->be->moveUploadedFileEx( $stFromPath, $i_stToPath );
    }


    public function name( string $i_stTag, ?int $i_niIndex = null ) : string {
        $this->check( $i_stTag, $i_niIndex );
        return $this->get( $i_stTag, 'name', $i_niIndex );
    }


    public function size( string $i_stTag, ?int $i_niIndex = null ) : int {
        $this->check( $i_stTag, $i_niIndex );
        return intval( $this->get( $i_stTag, 'size', $i_niIndex ) );
    }


    public function tmpName( string $i_stTag, ?int $i_niIndex = null ) : string {
        $this->check( $i_stTag, $i_niIndex );
        return $this->get( $i_stTag, 'tmp_name', $i_niIndex );
    }


    public function type( string $i_stTag, ?int $i_niIndex = null ) : string {
        $this->check( $i_stTag, $i_niIndex );
        return $this->rFiles[ $i_stTag ][ 'type' ];
    }


    public function validate( string $i_stTag, ?int $i_niIndex = null, ?string &$o_stReason = null ) : bool {
        if ( ! $this->hasAtAll( $i_stTag, $i_niIndex ) ) {
            $o_stReason = "No uploaded file for tag: {$i_stTag}";
            return false;
        }
        if ( $this->error( $i_stTag, $i_niIndex ) !== UPLOAD_ERR_OK || $this->name( $i_stTag, $i_niIndex ) === '' ) {
            $o_stReason = "Upload failed for {$i_stTag}: " . $this->errorString( $i_stTag, $i_niIndex );
            return false;
        }
        $tmpName = $this->tmpName( $i_stTag, $i_niIndex );
        if ( ! $this->be->isUploadedFile( $tmpName ) ) {
            $o_stReason = "File tmpName {$tmpName} is not an uploaded file for {$i_stTag}.";
            return false;
        }
        if ( ! $this->be->fileExists( $tmpName ) ) {
            $o_stReason = "File tmpName {$tmpName} does not exist for {$i_stTag}.";
            return false;
        }
        return true;
    }


    /** @suppress PhanAccessReadOnlyProperty Phan bug; see issue 4834. */
    private function check( string $i_stTag, ?int $i_niIndex ) : void {
        $stLabel = $this->label( $i_stTag, $i_niIndex );
        if ( ! array_key_exists( $i_stTag, $this->rFiles ) ) {
            throw new RuntimeException( "No file with tag {$stLabel}" );
        }
        if ( ! array_key_exists( 'name', $this->rFiles[ $i_stTag ] ) ) {
            throw new RuntimeException( "Upload {$stLabel} has no name." );
        }
        if ( ! is_int( $i_niIndex ) ) {
            if ( is_array( $this->rFiles[ $i_stTag ][ 'name' ] ) ) {
                throw new RuntimeException( "Upload {$stLabel} unexpectedly contains multiple files." );
            }
            return;
        }
        if ( ! is_array( $this->rFiles[ $i_stTag ][ 'name' ] ) ) {
            throw new RuntimeException( "Upload {$stLabel} unexpectedly contains only one file." );
        }
    }


    private function get( string $i_stTag, string $i_stField, ?int $niIndex ) : mixed {
        $this->check( $i_stTag, $niIndex );
        if ( is_int( $niIndex ) ) {
            return $this->rFiles[ $i_stTag ][ $i_stField ][ $niIndex ];
        }
        return $this->rFiles[ $i_stTag ][ $i_stField ];
    }


    private function hasAtAll( string $i_stTag, ?int $i_niIndex ) : bool {
        if ( ! array_key_exists( $i_stTag, $this->rFiles ) ) {
            return false;
        }
        if ( is_int( $i_niIndex ) ) {
            $x = $this->rFiles[ $i_stTag ][ 'name' ];
            if ( ! is_array( $x ) ) {
                return false;
            }
            return array_key_exists( $i_niIndex, $x );
        }
        $r = $this->rFiles[ $i_stTag ];
        if ( ! is_string( $r[ 'name' ] ) ) {
            return false;
        }
        return true;
    }


    private function label( string $i_stTag, ?int $i_niIndex ) : string {
        if ( is_int( $i_niIndex ) ) {
            return "{$i_stTag}[{$i_niIndex}]";
        }
        return $i_stTag;
    }


}
