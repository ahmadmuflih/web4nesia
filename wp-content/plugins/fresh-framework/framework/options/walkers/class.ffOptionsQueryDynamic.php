<?php

class ffOptionsQueryDynamic extends ffOptionsQuery {

    private $_getOptionsCallback = null;

    public function setGetOptionsCallback( $callback ) {
        $this->_getOptionsCallback = $callback;
    }

    public function setData( $data ) {
        $this->_setData( $data );
        $this->_setOptionsstructureHasBeenCompared( false );

        return $this;
    }



    protected function _compareDataWithStructure() {
		if ($this->_getOptionsstructureHasBeenCompared() == false && $this->_getOptionsCallback != null ) {
			$this->_setOptionsstructureHasBeenCompared(true);



            $options = call_user_func( $this->_getOptionsCallback );


//			$options = $this->_getOptionsHolder()->getOptions();


            $this->_getArrayConvertor()->setOptionsArrayData( $this->_data );
			$this->_getArrayConvertor()->setOptionsStructure( $options );
			$this->_data = $this->_getArrayConvertor()->walk();
			$this->_setOptionsstructureHasBeenCompared(true);
		} else if( $this->_getOptionsstructureHasBeenCompared() == false && $this->_getOptionsCallback == null ) {
			$this->_setOptionsstructureHasBeenCompared(true);
		}
	}

    public function getNew( $query = null ) {
        if( $this->_getPath() !== null && $query == null ) {
			$newQuery = $this->_getPath();

            if( $query != null ) {
                $newQuery .= ' ' . $query;
            }

            $query = $newQuery;
		}

		$query =  new ffOptionsQueryDynamic( $this->_data, $this->_getOptionsHolder(), $this->_getArrayConvertor(), $query, $this->_optionsStructureHasBeenCompared );
        $query->setGetOptionsCallback( $this->_getOptionsCallback );
		$query->setIteratorValidationCallback( $this->_getIteratorValidationCallback() );
		$query->setIteratorStartCallback( $this->_iteratorStartCallback );
		$query->setIteratorEndCallback( $this->_iteratorEndCallback );
		$query->setWPLayer( $this->_getWPLayer() );
		return $query;
	}
}