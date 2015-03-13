<?php

class ProfileController extends Centixx_Controller_Action
{
	/**
	 * Edycja własnego profilu (email, hasło)
	 * @throws Centixx_Acl_AuthenticationException
	 */
	public function indexAction()
	{

		$this->appendScript(array(
			'js/yetii.js'
		));
		$this->appendStyles(array(
			'styles/white.css',
			'styles/custom.css',
		));

		$user = $this->_currentUser;

		$form = new Application_Form_User_SelfEdit();

		$form->setValues(array(
			'user' => $user,
		));

		$this->view->header = 'Profil użytkownika ' . $user;
		$this->view->headTitle()->prepend($this->view->header);
		$this->view->user = $user;

		if ($this->getRequest()->isPost()) {

			$data = $this->getRequest()->getPost();
			if ($form->isValid($data)) {
				try {
					$user->setOptions($data)->save();
					$this->addFlashMessage('Dane zostały zaktualizowane', false, true);

					$session = new Zend_Session_Namespace('Zend_Auth');
					$session->storage = $user->getEmail();

				} catch (Zend_Db_Statement_Exception $e) {
					$form->getElement('email')->setErrors(array('' => 'Podany adres email jest już w użyciu'));
				}
			} else {
				$this->addFlashMessage('Formularz nie został poprawnie wypełniony', true, true);
			}

		} else {
			$form->setDefaults($user->toArray());
		}

		$this->view->editForm = $form;
	}
}