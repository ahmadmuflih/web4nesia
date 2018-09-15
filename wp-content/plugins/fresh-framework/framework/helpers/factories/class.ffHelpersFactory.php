<?php

class ffHelpersFactory extends ffFactoryAbstract {

    public function getStringHelper() {
        $this->_getClassloader()->loadClass('ffStringHelper');
        $stringHelper = new ffStringHelper();

        return $stringHelper;
    }

    public function getTableHelper() {
        $this->_getClassloader()->loadClass('ffTableHelper');
        $tableHelper = new ffTableHelper();

        return $tableHelper;
    }

}