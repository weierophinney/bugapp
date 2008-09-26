<?php
class Bugapp_Plugin_Auth extends Zend_Controller_Plugin_Abstract
{
    protected $_acl;

    /**
     * Dispatch loop startup plugin: get identity and acls
     * 
     * @param Zend_Controller_Request_Abstract $request 
     * @return void
     */
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

    /**
     * Get ACL lists
     * 
     * @return Zend_Acl
     */
    public function getAcl()
    {
        if (null === $this->_acl) {
            $acl = new Zend_Acl();
            $this->_loadAclClasses();
            $acl->add(new Bugapp_Acl_Resource_Bug)
                ->addRole(new Bugapp_Acl_Role_Guest)
                ->addRole(new Bugapp_Acl_Role_User, 'guest')
                ->addRole(new Bugapp_Acl_Role_Developer, 'user')
                ->addRole(new Bugapp_Acl_Role_Manager, 'developer')
                ->deny()
                ->allow('guest', 'bug', array('view', 'list', 'index'))
                ->allow('user', 'bug', array('comment', 'add', 'process-add'))
                ->allow('developer', 'bug', array('resolve'))
                ->allow('developer', 'bug', array('close', 'delete'));
            $this->_acl = $acl;
        }
        return $this->_acl;
    }

    /**
     * Load ACL classes from module models directory
     * 
     * @return void
     */
    protected function _loadAclClasses()
    {
        $loader = new Zend_Loader_PluginLoader(array(
            'Bugapp_Acl_Role'     => APPLICATION_PATH . '/models/Acl/Role/',
            'Bugapp_Acl_Resource' => APPLICATION_PATH . '/models/Acl/Resource/',
        ));
        foreach (array('Guest', 'User', 'Developer', 'Manager') as $role) {
            $loader->load($role);
        }
        foreach (array('Bug') as $resource) {
            $loader->load($resource);
        }
    }
}
