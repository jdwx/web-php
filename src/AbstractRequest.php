<?php


declare( strict_types = 1 );


namespace JDWX\Web;


use JDWX\Param\IParameter;
use JDWX\Param\IParameterSet;
use JDWX\Param\ParameterSet;
use OutOfBoundsException;


abstract class AbstractRequest implements IRequest {


    protected ParameterSet $setCookie;

    protected ParameterSet $setGet;

    protected ParameterSet $setPost;

    protected FilesHandler $files;


    public function COOKIE( string $i_stName, mixed $i_xDefault = null ) : ?IParameter {
        return $this->setCookie->get( $i_stName, $i_xDefault );
    }


    public function FILES() : FilesHandler {
        return $this->files;
    }


    public function GET( string $i_stName, mixed $i_xDefault = null ) : ?IParameter {
        return $this->setGet->get( $i_stName, $i_xDefault );
    }


    public function POST( string $i_stName, mixed $i_xDefault = null ) : ?IParameter {
        return $this->setPost->get( $i_stName, $i_xDefault );
    }


    public function _COOKIE() : IParameterSet {
        return $this->setCookie;
    }


    public function _GET() : IParameterSet {
        return $this->setGet;
    }


    public function _POST() : IParameterSet {
        return $this->setPost;
    }


    public function cookieEx( string $i_stName ) : IParameter {
        $np = $this->COOKIE( $i_stName );
        if ( $np instanceof IParameter ) {
            return $np;
        }
        throw new OutOfBoundsException( 'COOKIE parameter not found: ' . $i_stName );
    }


    /** @param string ...$i_rstNames */
    public function cookieHas( ...$i_rstNames ) : bool {
        return $this->setCookie->has( ...$i_rstNames );
    }


    public function getEx( string $i_stName ) : IParameter {
        $np = $this->GET( $i_stName );
        if ( $np instanceof IParameter ) {
            return $np;
        }
        throw new OutOfBoundsException( 'GET parameter not found: ' . $i_stName );
    }


    /** @param string ...$i_rstNames */
    public function getHas( ...$i_rstNames ) : bool {
        return $this->setGet->has( ...$i_rstNames );
    }


    public function postEx( string $i_stName ) : IParameter {
        $np = $this->POST( $i_stName );
        if ( $np instanceof IParameter ) {
            return $np;
        }
        throw new OutOfBoundsException( 'POST parameter not found: ' . $i_stName );
    }


    /** @param string ...$i_rstNames */
    public function postHas( ...$i_rstNames ) : bool {
        return $this->setPost->has( ...$i_rstNames );
    }


}
