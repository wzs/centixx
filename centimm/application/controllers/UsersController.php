<?php

class UsersController extends Centixx_Controller_Action
{
	public function indexAction()
	{
		$this->view->users = Centixx_Model_Mapper_User::factory()->fetchAll();
	}

	public function showAction()
	{
		$userId = $this->getRequest()->getParam('id');
		$user = Centixx_Model_Mapper_User::factory()->find($userId);

		if (!$user->isAllowed($this->_currentUser, Centixx_Model_Abstract::ACTION_SHOW)) {
			throw new Centixx_Acl_AuthenticationException();
		}

		$this->view->headTitle()->prepend($user . ' - ');
		$this->view->user = $user;
	}

	public function editAction()
	{
		$userId = $this->getRequest()->getParam('id');
		$user = Centixx_Model_Mapper_User::factory()->find($userId);

		if (!$user->isAllowed($this->_currentUser, 'edit')) {
			throw new Centixx_Acl_AuthenticationException();
		}

		//czy uzytkownik ma uprawnienia do dodania czlonka zarzadu
		$hasPermission = $this->_currentUser->hasPermission(Centixx_Model_Permission::TYPE_ADD_CEO);

		$form = new Application_Form_User_Edit();

		$roles = Centixx_Model_Mapper_Role::factory()->fetchAll();

		$form->setValues(array(
			'user' => $user,
			'roles' => $roles,
		));

		$this->view->headTitle()->prepend($user . ' - Edycja - ');
		$this->view->user = $user;

		if ($this->getRequest()->isPost()) {

			$data = $this->getRequest()->getPost();
			if ($form->isValid($data)) {

				//jezeli obecny uzytkownik nie ma nadanych uprawnien,
				//nie powinien miec mozliwosci zmiany uprawnien CEO
				if ($data['role'] !== $user->getRole() && $data['role'] == Centixx_Acl::ROLE_CEO) {
					if (!$hasPermission) {
						throw new Centixx_Acl_AuthenticationException("Nie masz uprawnień do dodania członka zarządu");
					}
					$this->_currentUser->removePermission(Centixx_Model_Permission::TYPE_ADD_CEO);
				}

				$user->setOptions($data)->save();
				$this->view->messages[] = 'Dane zostały zaktualizowane';
			}

		} else {
			$form->setDefaults($user->toArray());
		}

		$this->view->editForm = $form;
	}
}