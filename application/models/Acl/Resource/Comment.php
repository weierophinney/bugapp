<?php
class Bugapp_Acl_Resource_Comment implements Zend_Acl_Resource_Interface
{
    public function getResourceId()
    {
        return 'bug';
    }
}
