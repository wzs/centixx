<?php

class UsersController extends Centixx_Controller_Action
{
	public function indexAction()
	{
		$this->view->users = Centixx_Model_Mapper_Abstract::factory('user')->fetchAll();
		$this->view->permissions = Centixx_Model_Mapper_Abstract::factory('permission')->getPermissions($this->_currentUser);
	}

	public function showAction()
	{
		$userId = $this->getRequest()->getParam('id');
		$user = Centixx_Model_Mapper_Abstract::factory('user')->find($userId);

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
		$user = Centixx_Model_Mapper_Abstract::factory('user')->find($userId);

		if (!$user->isAllowed($this->_currentUser, 'delete')) {
			throw new Centixx_Acl_AuthenticationException();
		}

		$user->delete();
		$this->log(Centixx_Log::USER_DELETED, $user);
		$this->addFlashMessage('Użytkownik został usunięty', false, true);

		$this->_forward('index');

		if ($this->_isAjaxRequest) {
			echo json_encode(true);
		}
	}

	public function addAction()
	{
		$user = new Centixx_Model_User();
		$this->prepare($user, 'add');

		$this->view->headTitle()->prepend('Dodawanie użytkownika ');
		$this->view->header = 'Dodawanie użytkownika';

	}

	public function editAction()
	{
		$userId = $this->getRequest()->getParam('id');
		$user = Centixx_Model_Mapper_Abstract::factory('user')->find($userId);

		$this->prepare($user, 'edit');

		$this->view->headTitle()->prepend($user . ' - Edycja - ');
		$this->view->header = 'Edycja użytkownika';
	}

	protected function prepare(Centixx_Model_User $user, $type)
	{
		if (!$user->isAllowed($this->_currentUser, Centixx_Model_Abstract::ACTION_UPDATE)) {
			throw new Centixx_Acl_AuthenticationException();
		}

		$this->_helper->viewRenderer('edit');

		$form = new Application_Form_User_Edit();

		$roles = Centixx_Model_Mapper_Abstract::factory('Role')->fetchAll();
		$departments = Centixx_Model_Mapper_Abstract::factory('Department')->fetchAll();
		$form->setValues(array(
			'roles' 		=> $roles,
			'user' 			=> $user,
			'departments' 	=> $departments,
		));

		$this->view->editForm = $form;

		if ($this->getRequest()->isPost()) {

			$data = $this->getRequest()->getPost();
			if ($form->isValid($data)) {

				if ($type == 'add' && !$user->isAllowed($this->_currentUser, Centixx_Model_User::ACTION_CREATE)) {
					throw new Centixx_Acl_AuthenticationException('Nie masz uprawnień do tworzenia nowych użytkowników');
				}

				//aby zmienić uprawnienia użytkownika na CEO / zmniejszyć uprawnienia CEO
				//obecnie zalogowany user MUSI mieć nadane pozwolenie
				if (
					($user->hasRole(Centixx_Acl::ROLE_CEO) && !in_array(Centixx_Acl::ROLE_CEO, $data['roles'])) ||
					(!$user->hasRole(Centixx_Acl::ROLE_CEO) && in_array(Centixx_Acl::ROLE_CEO, $data['roles']))
				) {
					if (!$user->isAllowed($this->_currentUser, Centixx_Model_User::ACTION_ADD_CEO)) {
						throw new Centixx_Acl_AuthenticationException("Nie masz uprawnień do edycji członka zarządu");
					}
					$this->_currentUser->removePermission(Centixx_Model_User::ACTION_ADD_CEO);
				}

				$user->setOptions($data)->save();

				if ($data['department']) {
					$department = Centixx_Model_Mapper_Abstract::factory('Department')->find($data['department']);
					$department->setManager($user)->save();
				}

				if ($type == 'add') {
					$this->log(Centixx_Log::USER_CREATED, $user);
					$this->addFlashMessage('Użytkownik został dodany', false, true);
				} else {
					$this->addFlashMessage('Dane zostały zaktualizowane', false, true);
					$this->log(Centixx_Log::USER_UPDATED, $user);
				}
				$this->_redirect($this->redirect('users'));


			} else {
				$this->addFlashMessage('Formularz wypełniony niepoprawnie', true, true);
				$form->setDefaults($data);
			}
		} else {
				$form->setDefaults($user->toArray());

				//potrzebne ze wzgledu na "dziwne" zachowanie multicheckboksa
				$form->setDefaults(array(
					'roles' => array_keys($user->getRoles()),
				));
		}


		$this->view->user = $user;
	}
}