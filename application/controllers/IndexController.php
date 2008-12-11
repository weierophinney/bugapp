<?php
class IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
    }

    public function __call($method, $args)
    {
        if ('Action' == substr($method, -6)) {
            echo "Why is $method being called?";
            return;
        }
        return parent::__call($method, $args);
    }
}
