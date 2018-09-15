<?php

class ffThemeBuilderBlockFactory extends ffFactoryAbstract {
    public function createBlock( $blockClassName ) {
        $this->_getClassloader()->loadClass( $blockClassName );

        $optionsExtender = new ffThemeBuilderOptionsExtender();

        $newClass = new $blockClassName( $optionsExtender );
        return $newClass;
    }
}