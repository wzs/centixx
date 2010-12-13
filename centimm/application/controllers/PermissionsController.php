<?php

class PermissionsController extends Centixx_Controller_Action
{
	public function indexAction()
	{
		//pracownicy HR
		$hrUsers = Centixx_Model_Mapper_User::factory()->fetchUsersByRole(Centixx_Acl::ROLE_HR);
		$form = new Application_Form_Permission_AddCeo();
		$form->setValues(array('users' => $hrUsers));

		$this->view->form = $form;

		$request = $this->getRequest();
		if ($request->isPost() && $form->isValid($request->getPost())) {
			$data = $form->getValues() + array(
				'from' => $this->_currentUser,
				'type' => Centixx_Model_Permission::TYPE_ADD_CEO,
				'count' => 1,
			);

			try {
				$permission = new Centixx_Model_Permission($data);
				$permission->save();

				$this->view->messages[] = 'Uprawnienie zostało nadane';
			} catch (Zend_Db_Statement_Exception $e) {
				//zapewniona unikalnosc odpowiednich kluczy  =na poziomie bazy danych
				$this->view->messages[] = 'Uprawnienie zostało nadane już wcześniej';
			} catch (Exception $e) {
				$this->view->messages[] = 'Wystąpił błąd - uprawnienie nie zostało nadane';
			}
		}
	}
}