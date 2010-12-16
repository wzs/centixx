<?php

class GroupsController extends Centixx_Controller_Action
{
	public function indexAction()
	{
		$this->view->groups = Centixx_Model_Mapper_Group::factory()->fetchAll();
	}

	public function deleteAction()
	{
		$groupId = $this->getRequest()->getParam('group_id');
		$group = Centixx_Model_Mapper_Group::factory()->find($groupId);

		if (!$group->isAllowed($this->_currentUser, Centixx_Model_Abstract::ACTION_UPDATE)) {
			throw new Centixx_Acl_AuthenticationException();
		}
		$group->delete();

		$this->addFlashMessage('Grupa została usunięta');
		$this->_redirect('groups');

	}

	public function showAction()
	{
		$groupId = $this->getRequest()->getParam('id');
		$group = Centixx_Model_Mapper_Group::factory()->find($groupId);

		if (!$group->isAllowed($this->_currentUser, Centixx_Model_Abstract::ACTION_READ)) {
			throw new Centixx_Acl_AuthenticationException();
		}

		$this->view->headTitle()->prepend('Grupa ' . $group . ' - ');
		$this->view->group = $group;
	}

	public function adduserAction()
	{
		$request = $this->getRequest();
		if ($request->isPost()) {

			$groupId = $request->getParam('id');
			$userId =  $request->getParam('new_item');

			$user  = Centixx_Model_Mapper_User::factory()->find($userId);
			$group = Centixx_Model_Mapper_Group::factory()->find($groupId);

			if (!$group->isAllowed($this->_currentUser, Centixx_Model_Abstract::ACTION_UPDATE)) {
				throw new Centixx_Acl_AuthenticationException();
			}

			$group->addUser($user);
			$this->_forward('edit', null, null, $request->getParams());
			$this->_redirect($group->getUrl('edit'));
		} else {
			$this->_forward('show', null, null, $request->getParams());
		}

	}

	protected function prepare($group, $mode)
	{
		$this->_helper->viewRenderer('edit');


		if (!$group->isAllowed($this->_currentUser, Centixx_Model_Abstract::ACTION_UPDATE)) {
			throw new Centixx_Acl_AuthenticationException();
		}

		$availableUsers = Centixx_Model_Mapper_User::factory()->fetchForGroup($group);
		$form = new Application_Form_Group_Edit();
		$form->setValues(array('group' => $group, 'availableUsers' => $availableUsers));

		//drugi warunek jest po to, aby po akcji dodania uzytkownika
		if ($this->getRequest()->isPost()) {
			try {
				$data = $this->getRequest()->getPost();
				if ($form->isValid($data)) {
					$group->setOptions($data)->save();

					if ($mode == 'edit') {
						$this->addFlashMessage('Dane grupy zostały zaktualizowane');
					} else {
						$this->addFlashMessage('Grupa została utworzona');
					}
					$this->_redirect($group->getUrl('edit'));
				}
			} catch (Exception $e) {
				echo $e->getMessage();
			}
		} else {
			$form->setDefaults($group->toArray());
			//potrzebne ze wzgledu na "dziwne" zachowanie multicheckboksa
			$form->setDefaults(array(
				'users' => $group->users ? array_keys($group->users) : null,
				'manager' => $group->manager->id,
			));
		}


		$this->view->form = $form;
		$this->view->formAction = $mode;
		$this->view->group = $group;
	}

	public function newAction()
	{
		$group = new Centixx_Model_Group();
		$group->project = $this->_currentUser->project;

		$this->prepare($group, 'new');
	}

	public function editAction()
	{
		$groupId = $this->getRequest()->getParam('id');
		$group = Centixx_Model_Mapper_Group::factory()->find($groupId);

		$this->prepare($group, 'edit');

//		$usersFromOtherGroups = Centixx_Model_Mapper_User::factory()->fetchAvailableUsers($group);
//
//		$addUserForm = new Centixx_Form_AddItem();
//		$addUserForm->submitLabel = 'Przypisz';
//		$addUserForm->setValues(array('items' => $usersFromOtherGroups));
//
//
//		$this->view->addUserForm = $addUserForm;

	}
}