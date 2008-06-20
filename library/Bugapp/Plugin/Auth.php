<?php
class Bugapp_Plugin_Auth extends Zend_Controller_Plugin_Abstract
{
    protected $_acl;

    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        $view   = Zend_Layout::getMvcInstance()->getView();
        $auth   = Zend_Auth::getInstance();
        $values = array(
            'user_id'    => null,
            'user_name'  => null,
            'user_email' => null,
        );
        if ($auth->hasIdentity()) {
            $identity = $auth->getIdentity();
            $values = array(
                'user_id'    => $identity->id,
                'user_name'  => $identity->username,
                'user_email' => $identity->email,
            );
            $role = empty($identity->role) ? 'user' : $identity->role;
        } else {
            $role = 'guest';
        }

        $view->assign($values);
        Zend_Registry::set('acl', $this->getAcl());
        Zend_Registry::set('role', $role);
    }

    public function getAcl()
    {
        if (null === $this->_acl) {
            $acl = new Zend_Acl();
            $acl->add(new Bugapp_Acl_Resource_Bug)
                ->addRole(new Bugapp_Acl_Role_Guest)
                ->addRole(new Bugapp_Acl_Role_User, 'guest')
                ->addRole(new Bugapp_Acl_Role_Developer, 'user')
                ->addRole(new Bugapp_Acl_Role_Manager, 'developer')
                ->deny()
                ->allow('guest', 'bug', array('view', 'list', 'index'))
                ->allow('user', 'bug', array('comment', 'add', 'add-process'))
                ->allow('developer', 'bug', array('resolve'))
                ->allow('developer', 'bug', array('close', 'delete'));
            $this->_acl = $acl;
        }
        return $this->_acl;
    }
}
