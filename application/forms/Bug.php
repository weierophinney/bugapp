<?php
class Bugapp_Form_Bug extends Zend_Form
{
    public function init()
    {
        $helper = Zend_Controller_Action_HelperBroker::getStaticHelper('getModel');
        $model  = $helper->getModel('bug');

        $priorities = $model->getPriorities();
        $types      = $model->getTypes();

        $summary = $this->addElement('text', 'summary', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('StringLength', false, array(0, 255)),
            ),
            'required'   => true,
            'label'      => 'Summary:',
        ));

        $typeId = $this->addElement('select', 'type_id', array(
            'validators'   => array(
                array('InArray', false, array(array_keys($types))),
            ),
            'required'     => true,
            'multiOptions' => $types,
            'label'        => 'Issue Type:',
        ));

        $priorityId = $this->addElement('select', 'priority_id', array(
            'validators'   => array(
                array('InArray', false, array(array_keys($priorities))),
            ),
            'required'     => true,
            'multiOptions' => $priorities,
            'label'        => 'Priority:',
        ));

        $description = $this->addElement('textarea', 'description', array(
            'filters'    => array('StringTrim'),
            'required'   => true,
            'label'      => 'Bug Description:',
        ));

        $report = $this->addElement('submit', 'report', array(
            'required' => false,
            'ignore'   => true,
            'label'    => 'Report Issue',
        ));
    }
}
