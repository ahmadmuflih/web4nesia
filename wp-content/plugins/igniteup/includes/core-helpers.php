<?php

/*
 * 
 * igniteup_head global function
 * 
 */

function igniteup_head() {
    CSComingSoonCreator::perfomIgniteUpHead();
}

/*
 * 
 * igniteup_footer global function
 * 
 */

function igniteup_footer() {
    CSComingSoonCreator::perfomIgniteUpFooter();
}

/*
 * 
 * igniteup_get_option global function
 * 
 */

function igniteup_get_option($key, $default = NULL) {
    $value = CSComingSoonCreator::igniteUpGetOption($key, $default);
    return $value;
}
