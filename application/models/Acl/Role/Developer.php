<?php
class Bugapp_Acl_Role_Developer implements Zend_Acl_Role_Interface
{
    public function getRoleId()
    {
        return 'developer';
    }
}
