<?php
class Bugapp_Helper_GetForm extends Zend_Controller_Action_Helper_Abstract
{
    protected $_forms = array();

    /**
     * Load and return a form object
     * 
     * @param  string $form 
     * @param  Zend_Config|array $config 
     * @return Zend_Form
     */
    public function getForm($form, $config = null)
    {
        $form = ucfirst($form);
        $class = 'Bugapp_Form_' . $form;
        if (!array_key_exists($class, $this->_forms)) {
            $this->_forms[$class] = new $class($config);
        }
        return $this->_forms[$class];
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

