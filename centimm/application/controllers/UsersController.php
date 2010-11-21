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

		$form = new Application_Form_User_Edit();

		$form->setValues(array(
			'user' => $user,
			'roles' => Centixx_Model_Mapper_Role::factory()->fetchAll()
		));

		$this->view->headTitle()->prepend($user . ' - Edycja - ');
		$this->view->user = $user;

		if ($this->getRequest()->isPost()) {
			try {
				$data = $this->getRequest()->getPost();
				if ($form->isValid($data)) {
					$user->setOptions($data)->save();
					$this->view->messages[] = 'Dane zostały zaktualizowane';
				}
			} catch (Exception $e) {
				echo $e->getMessage();
			}
		} else {
			$form->setDefaults($user->toArray());
		}

		$this->view->editForm = $form;
	}
}