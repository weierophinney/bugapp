<?php
class Zend_View_Helper_CheckAcl
{
    public $view;

    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }

    public function checkAcl($resource, $right)
    {
        $acl  = Zend_Registry::get('acl');
        $role = $this->view->getRole();
        if (!$acl->has($resource)) {
            return true;
        }
        return $acl->isAllowed($role, $resource, $right);
    }
}
