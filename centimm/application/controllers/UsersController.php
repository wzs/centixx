<?php

class UsersController extends Centixx_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->_forward('list');
    }

    public function listAction()
    {
        $mapper = new Centixx_Model_Mapper_User();
		$this->view->users = $mapper->fetchAll();
    }

    public function showAction()
    {
    	$userId = $this->getRequest()->getParam('id');
		$mapper = new Centixx_Model_Mapper_User();
		$user = $mapper->find($userId);

		$this->view->headTitle()->prepend((string)$user . ' - ');
		$this->view->user = $user;
    }
}





