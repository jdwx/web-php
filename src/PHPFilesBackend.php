<?php


declare( strict_types = 1 );


namespace JDWX\Web;


class PHPFilesBackend extends AbstractFilesBackend {


    public function fileExists( string $i_stPath ) : bool {
        return file_exists( $i_stPath );
    }


    public function fileGetContents( string $i_stPath ) : string|false {
        return file_get_contents( $i_stPath );
    }


    public function isUploadedFile( string $i_stPath ) : bool {
        return is_uploaded_file( $i_stPath );
    }


    public function moveUploadedFile( string $i_stFrom, string $i_stTo ) : bool {
        return move_uploaded_file( $i_stFrom, $i_stTo );
    }


}
