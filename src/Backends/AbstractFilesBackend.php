<?php


declare( strict_types = 1 );


namespace JDWX\Web\Backends;


use RuntimeException;


abstract class AbstractFilesBackend implements FilesBackendInterface {


    final public function fileGetContentsEx( string $i_stPath ) : string {
        $stContent = $this->fileGetContents( $i_stPath );
        if ( false === $stContent ) {
            throw new RuntimeException( "Unable to read file: {$i_stPath}" );
        }
        return $stContent;
    }


    final public function moveUploadedFileEx( string $i_stFrom, string $i_stTo ) : void {
        if ( $this->moveUploadedFile( $i_stFrom, $i_stTo ) ) {
            return;
        }
        throw new RuntimeException( 'Unable to move uploaded file.' );
    }


}
