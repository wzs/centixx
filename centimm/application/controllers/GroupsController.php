<?php

class GroupsController extends Centixx_Controller_Action
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
        $mapper = new Centixx_Model_Mapper_Group();
		$this->view->groups = $mapper->fetchAll();
    }

    public function showAction()
    {
    	$groupId = $this->getRequest()->getParam('id');
		$mapper = new Centixx_Model_Mapper_Group();
		$group = $mapper->find($groupId);
		$this->view->headTitle()->prepend('Grupa ' . $group . ' - ');
		$this->view->group = $group;

		$userMapper = new Centixx_Model_Mapper_User();
		$users = $userMapper->fetchUsersFromOtherGroups($group);

		$this->view->form = new Application_Form_Group_AddUser();
		$this->view->form->setUsers($users);
    }

    public function adduserAction()
    {
    	$request = $this->getRequest();
		if ($request->isPost()) {

			$groupId = $request->getParam('id');
			$userId =  $request->getParam('new_user');

			$userMapper = new Centixx_Model_Mapper_User();
			$user = $userMapper->find($userId)->setGroup($groupId)->save();

			$this->_logger->log("{$this->_currentUser} przypisał użytkownika {$user} do grupy {$user->group}", Centixx_Log::CENTIXX);
			$this->_forward('show', null, null, $request->getParams());
		} else {
			$this->_forward('show', null, null, $request->getParams());
		}
    }
}