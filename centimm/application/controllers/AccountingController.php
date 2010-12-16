<?php

class AccountingController extends Centixx_Controller_Action
{
	public function indexAction()
	{
		$this->view->users = Centixx_Model_Mapper_User::factory()->fetchAll();
//		$grid = Bvb_Grid::factory('table',array(),'');
//
//		$grid->setImagesUrl($this->_config['resources']['layout']['basePath'] . 'Bvb/extras/images/');
//
//		$select = $this->_db->select()
//			->from(
//				array('u' => 'users'),
//				array(
//					'Nr kadrowy' => 'u.user_id',
//					'Nazwisko' => 'user_surname',
//					'Imię' => 'user_name',
//					'E-mail' => 'user_email',
//					'Nr konta' => 'user_account'))
//			->join(
//				array('r' => 'roles'),
//				'u.user_role = r.role_id',
//				array('Stanowisko' => 'role_name')
//			)
//			->join(
//				array('g' => 'groups'),
//				'u.user_group = g.group_id',
//				array('Grupa' => 'group_name')
//			);
//
//		$grid->setSource(new Bvb_Grid_Source_Zend_Select($select));
//		//do czego export
//		$grid->setExport(array('xml','odt','pdf'));
//
//		$form = new Bvb_Grid_Form('Application_Form_User_Edit', array());
//		$form->setAdd(true);
//    	$form->setEdit(true);
//    	$form->setDelete(true);
//    	$form->setAddButton(true);
//
//    	$grid->setForm($form);

//		$this->view->grid = $grid->deploy();
	}

public function showAction()
	{
		$userId = $this->getRequest()->getParam('id');
		$user = Centixx_Model_Mapper_User::factory()->find($userId);

		if (!$user->isAllowed($this->_currentUser, Centixx_Model_Abstract::ACTION_SHOW)) {
			throw new Centixx_Acl_AuthenticationException();
		}

		$this->view->headTitle()->prepend($user . ' - ');
		$this->view->header = 'Edycja użytkownika' . $user;
		$this->view->user = $user;
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
				$user->setMapper(new Centixx_Model_Mapper_User());

				if (!$user->isAllowed($this->_currentUser, Centixx_Model_User::ACTION_EDIT)) {
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
				$this->addFlashMessage('Użytkownik został dodany');
			}
		}

		$this->view->editForm = $form;

	}

	public function editAction()
	{
		$userId = $this->getRequest()->getParam('id');
		$user = Centixx_Model_Mapper_User::factory()->find($userId);

		if (!$user->isAllowed($this->_currentUser, 'edit')) {
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

				//jezeli obecny uzytkownik nie ma nadanych uprawnien,
				//nie powinien miec mozliwosci zmiany uprawnien CEO
				if ($data['role'] !== $user->getRole() && $data['role'] == Centixx_Acl::ROLE_CEO) {
					if ($user->isAllowed($this->_currentUser, Centixx_Model_User::ACTION_ADD_CEO)) {
						throw new Centixx_Acl_AuthenticationException("Nie masz uprawnień do dodania członka zarządu");
					}
					$this->_currentUser->removePermission(Centixx_Model_User::ACTION_ADD_CEO);
				}

				$user->setOptions($data)->save();
				$this->addFlashMessage('Dane zostały zaktualizowane');
			}

		} else {
			$form->setDefaults($user->toArray());
		}

		$this->view->editForm = $form;
	}

	public function deleteAction(){
		$userId = $this->getRequest()->getParam('id');

		$user = Centixx_Model_Mapper_User::factory()->find($userId);

		if (!$user->isAllowed($this->_currentUser, 'delete')) {
			throw new Centixx_Acl_AuthenticationException();
		}
		else{
			$user = Centixx_Model_Mapper_User::factory()->delete($user);
		}
		$this->_redirect('staffing');
	}
}