<?php
/*
Plugin Name: Fresh Framework
Plugin URI: http://freshface.net
Description: Fresh Framework plugin is an engine that powers our Fresh Themes and Plugins
Version: 1.9.17
Author: FRESHFACE
Author URI: http://freshface.net
License: It is NOT allowed to use this fresh-framework plugin or it's code to create a new product.
*/

require_once dirname(__FILE__).'/framework/init/class.ffFrameworkVersionManager.php';

ffFrameworkVersionManager::getInstance()->addVersion('1.9.17', dirname(__FILE__).'/framework/bootstrap.php', dirname(__FILE__), plugin_dir_url(dirname(__FILE__).'/freshplugin.php') );
