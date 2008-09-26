<?php
class Bugapp_Helper_GetForm extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * @var array Form instances
     */
    protected $_forms = array();

    /**
     * @var Zend_Loader_PluginLoader
     */
    protected $_loader;

    /**
     * Initialize plugin loader for forms
     * 
     * @return void
     */
    public function __construct()
    {
        $this->_loader = new Zend_Loader_PluginLoader(array(
            'Bugapp_Form' => APPLICATION_PATH . '/forms',
        ));
    }

    /**
     * Load and return a form object
     * 
     * @param  string $form 
     * @param  Zend_Config|array $config 
     * @return Zend_Form
     */
    public function getForm($form, $config = null)
    {
        if (!array_key_exists($form, $this->_forms)) {
            $class = $this->_loader->load($form);
            $this->_forms[$form] = new $class($config);
        }
        return $this->_forms[$form];
    }

    /**
     * Proxy to getForm()
     * 
     * @param  string $form 
     * @param  array|Zend_Config|null $config 
     * @return Zend_Form
     */
    public function direct($form, $config = null)
    {
        return $this->getForm($form, $config);
    }
}

