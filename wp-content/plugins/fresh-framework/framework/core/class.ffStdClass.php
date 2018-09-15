<?php

class ffStdClass extends stdClass {
    public function getArray() {
        return (array)$this;
    }
}