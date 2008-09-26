<?php
class Bugapp_Acl_Resource_Bug implements Zend_Acl_Resource_Interface
{
    public function getResourceId()
    {
        return 'bug';
    }
}
