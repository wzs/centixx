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


		if (!$group->isAllowed($this->_currentUser, 'edit')) {
			throw new Centixx_Acl_AuthenticationException();
		}
		$group->delete();

		$this->_flashMessenger->addMessage('Grupa została usunięta');
		$this->_redirect('groups');

	}

	public function showAction()
	{
		$groupId = $this->getRequest()->getParam('id');
		$group = Centixx_Model_Mapper_Group::factory()->find($groupId);

		if (!$group->isAllowed($this->_currentUser, 'view')) {
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

			if (!$group->isAllowed($this->_currentUser, 'edit')) {
				throw new Centixx_Acl_AuthenticationException();
			}

			$group->addUser($user);

			$this->_logger->log(
				"{$this->_currentUser} przypisał użytkownika {$user} do grupy {$group}",
			Centixx_Log::CENTIXX
			);
			$this->_forward('edit', null, null, $request->getParams());
			$this->_redirect($group->getUrl('edit'));
		} else {
			$this->_forward('show', null, null, $request->getParams());
		}
	}

	public function editAction()
	{
		$groupId = $this->getRequest()->getParam('id');
		$group = Centixx_Model_Mapper_Group::factory()->find($groupId);

		$usersFromOtherGroups = Centixx_Model_Mapper_User::factory()->fetchUngroupedUsers($group);

		if (!$group->isAllowed($this->_currentUser, 'edit')) {
			throw new Centixx_Acl_AuthenticationException();
		}

		$form = new Application_Form_Group_Edit();
		$form->setValues(array('group' => $group));

		$addUserForm = new Centixx_Form_AddItem();
		$addUserForm->submitLabel = 'Przypisz';
		$addUserForm->setValues(array('items' => $usersFromOtherGroups));

		//drugi warunek jest po to, aby po akcji dodania uzytkownika
		if ($this->getRequest()->isPost()) {
			try {
				$data = $this->getRequest()->getPost();
				if ($form->isValid($data)) {
					$group->setOptions($data)->save();
					$this->view->messages[] = 'Dane zostały zaktualizowane';
				}
			} catch (Exception $e) {
				echo $e->getMessage();
			}
		} else {
			$form->setDefaults($group->toArray());
		}

		$this->view->form = $form;
		$this->view->addUserForm = $addUserForm;
		$this->view->group = $group;
	}
}