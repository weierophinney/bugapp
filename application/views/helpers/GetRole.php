<?php
class Zend_View_Helper_GetRole
{
    public function getRole()
    {
        return Zend_Registry::get('role');
    }
}

