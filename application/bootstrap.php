<?php
defined('APPLICATION_PATH') 
    or define('APPLICATION_PATH', realpath(dirname(__FILE__)));
defined('APPLICATION_STATE') 
    or define('APPLICATION_STATE', 'development');

require_once APPLICATION_PATH . '/plugins/Initialize.php';

$front = Zend_Controller_Front::getInstance();
$front->registerPlugin(new Bugapp_Plugin_Initialize(APPLICATION_STATE, APPLICATION_PATH))
      ->addControllerDirectory(APPLICATION_PATH . '/controllers');
