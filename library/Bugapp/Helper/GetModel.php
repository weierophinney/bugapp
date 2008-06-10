<?php
class Bugapp_Helper_GetModel extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Load a model class and return an object instance
     * 
     * @param  string $model 
     * @return object
     */
    public function getModel($model)
    {
        $model     = ucfirst($model);
        $module    = $this->_getModuleName();
        $className = 'Model_' . $model;

        if ('default' != $module) {
            $className = ucfirst($module) . '_' . $className;
        }
        if (class_exists($className, false)) {
            return new $className();
        }

        $modulePath    = $this->_getModulePath($module);
        $modelFileName = str_replace('_', '/', $model);
        $modelFileName = $modulePath . '/models/' . $modelFileName . '.php';
        if (!Zend_Loader::isReadable($modelFileName)) {
            throw new Exception(sprintf('Invalid model "%" specified; model class file not found', $model));
        }
        Zend_Loader::loadFile($modelFileName);
        return new $className;
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

    /**
     * Get module name
     * 
     * @return string
     */
    protected function _getModuleName()
    {
        $module = $this->getRequest()->getModuleName();
        if (empty($module)) {
            $module = 'default';
        }
        return $module;
    }

    /**
     * Get module path
     * 
     * @param  string|null $module 
     * @return string
     */
    protected function _getModulePath($module = null)
    {
        if (null === $module) {
            $module = $this->_getModuleName();
        }

        return realpath($this->getFrontController()->getControllerDirectory($module) . '/../');
    }
}
