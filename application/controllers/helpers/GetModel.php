<?php
class Bugapp_Helper_GetModel extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * @var Zend_Loader_PluginLoader
     */
    protected $_loader;

    /**
     * Initialize plugin loader for models
     * 
     * @return void
     */
    public function __construct()
    {
        $this->_loader = new Zend_Loader_PluginLoader(array(
            'Bugapp' => APPLICATION_PATH . '/models',
        ));
    }

    /**
     * Load a model class and return an object instance
     * 
     * @param  string $model 
     * @return object
     */
    public function getModel($model)
    {
        $class = $this->_loader->load($model);
        return new $class;
    }

    /**
     * Proxy to getModel()
     * 
     * @param  string $model 
     * @return object
     */
    public function direct($model)
    {
        return $this->getModel($model);
    }
}
