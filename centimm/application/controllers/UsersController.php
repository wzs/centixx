<?php

class UsersController extends Centixx_Controller_Action
{
	public function indexAction()
	{
		$this->view->users = Centixx_Model_Mapper_User::factory()->fetchAll();
		$this->view->permissions = Centixx_Model_Mapper_Permission::factory()->getPermissions($this->_currentUser);
	}

	public function showAction()
	{
		$userId = $this->getRequest()->getParam('id');
		$user = Centixx_Model_Mapper_User::factory()->find($userId);

		if (!$user->isAllowed($this->_currentUser, Centixx_Model_Abstract::ACTION_READ)) {
			throw new Centixx_Acl_AuthenticationException();
		}

		$this->view->headTitle()->prepend($user . ' - ');
		$this->view->header = 'Edycja użytkownika' . $user;
		$this->view->user = $user;
	}

	public function deleteAction()
	{

		$userId = $this->getRequest()->getParam('id');
		$user = Centixx_Model_Mapper_User::factory()->find($userId);

		if (!$user->isAllowed($this->_currentUser, 'delete')) {
			throw new Centixx_Acl_AuthenticationException();
		}

		$user->delete();
		$this->log(Centixx_Log::USER_DELETED, $user);
		$this->view->messages[] = 'Użytkownik został usunięty';

		$this->_forward('index');

		if ($this->_isAjaxRequest) {
			echo json_encode(true);
		}
	}

	public function addAction()
	{
		$this->_helper->viewRenderer('edit');

		$form = new Application_Form_User_Edit();

		$roles = Centixx_Model_Mapper_Role::factory()->fetchAll();

		//przy dodawaniu użytkownika ustawiana jest mu domyślna rola
		$form->setValues(array(
			'roles' => null,
		));

		$this->view->headTitle()->prepend('Dodawanie użytkownika ');
		$this->view->header = 'Dodawanie użytkownika';

		if ($this->getRequest()->isPost()) {

			$data = $this->getRequest()->getPost();
			if ($form->isValid($data)) {
				$user = new Centixx_Model_User();

				if (!$user->isAllowed($this->_currentUser, Centixx_Model_User::ACTION_CREATE)) {
					throw new Centixx_Acl_AuthenticationException('Nie masz uprawnień do tworzenia nowych użytkowników');
				}

				//jezeli obecny uzytkownik nie ma nadanych uprawnien,
				//nie powinien miec mozliwosci zmiany uprawnien CEO
				if ($data['role'] == Centixx_Acl::ROLE_CEO) {
					if ($user->isAllowed($this->_currentUser, Centixx_Model_User::ACTION_ADD_CEO)) {
						throw new Centixx_Acl_AuthenticationException("Nie masz uprawnień do dodania członka zarządu");
					}
					$this->_currentUser->removePermission(Centixx_Model_User::ACTION_ADD_CEO);
				}

				$user->setOptions($data)->save();
				$this->log(Centixx_Log::USER_CREATED, $user);
				$this->view->messages[] = 'Użytkownik został dodany';
				$this->_forward('index');
			}
		}

		$this->view->editForm = $form;

	}

	public function editAction()
	{
		$userId = $this->getRequest()->getParam('id');
		$user = Centixx_Model_Mapper_User::factory()->find($userId);

		if (!$user->isAllowed($this->_currentUser, Centixx_Model_Abstract::ACTION_UPDATE)) {
			throw new Centixx_Acl_AuthenticationException();
		}

		$form = new Application_Form_User_Edit();

		$roles = Centixx_Model_Mapper_Role::factory()->fetchAll();

		$form->setValues(array(
			'user' => $user,
			'roles' => $roles,
		));

		$this->view->headTitle()->prepend($user . ' - Edycja - ');
		$this->view->header = 'Edycja użytkownika';

		$this->view->user = $user;

		if ($this->getRequest()->isPost()) {

			$data = $this->getRequest()->getPost();
			if ($form->isValid($data)) {

				//aby zmienić uprawnienia użytkownika na CEO / zmniejszyć uprawnienia CEO
				//obecnie zalogowany user MUSI mieć nadane pozwolenie
				if ($data['role'] !== $user->getRole()
				&& ($data['role'] == Centixx_Acl::ROLE_CEO || $user->role == Centixx_Acl::ROLE_CEO)
				) {
					if (!$user->isAllowed($this->_currentUser, Centixx_Model_User::ACTION_ADD_CEO)) {
						throw new Centixx_Acl_AuthenticationException("Nie masz uprawnień do edycji członka zarządu");
					}
					$this->_currentUser->removePermission(Centixx_Model_User::ACTION_ADD_CEO);
				}

				$user->setOptions($data)->save();
				$form->setDefaults($user->toArray());
				$this->view->messages[] = 'Dane zostały zaktualizowane';
				$this->log(Centixx_Log::USER_UPDATED, $user);
			}
		} else {
			$form->setDefaults($user->toArray());
		}

		$this->view->editForm = $form;
	}

	/**
	 * Edycja własnego profilu (email, hasło)
	 * @throws Centixx_Acl_AuthenticationException
	 */
	public function selfeditAction()
	{
		$user = $this->_currentUser;
		//		if (!$user->isAllowed($this->_currentUser, Centixx_Model_Abstract::ACTION_UPDATE)) {
		//			throw new Centixx_Acl_AuthenticationException();
		//		}

		$form = new Application_Form_User_SelfEdit();

		$form->setValues(array(
			'user' => $user,
		));

		$this->view->headTitle()->prepend($user . ' - Edycja - ');
		$this->view->header = 'Edycja własnego profilu';
		$this->view->user = $user;

		if ($this->getRequest()->isPost()) {

			$data = $this->getRequest()->getPost();
			if ($form->isValid($data)) {
				try {
					$user->setOptions($data)->save();
					$this->view->messages[] = 'Dane zostały zaktualizowane';
				} catch (Zend_Db_Statement_Exception $e) {
					$form->getElement('email')->setErrors(array('' => 'Podany adres email jest już w użyciu'));
				}
			}

		} else {
			$form->setDefaults($user->toArray());
		}

		$this->view->editForm = $form;
	}
}