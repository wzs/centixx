<?php

class AuthController extends Centixx_Controller_Action
{
	public function indexAction()
	{

		$db = $this->_getParam('db');
		$request = $this->getRequest();
		$loginForm = new Application_Form_Auth_Login();

		if ($request->isPost()) {
			if ($loginForm->isValid($request->getPost())) {
				$auth = $this->_authenticate($loginForm);
				if ($auth->isValid()) {
					$this->_setCurrentUser($auth->getIdentity());

					$this->addFlashMessage('Zalogowałeś się', false, true);
					$this->log(Centixx_Log::LOGIN_SUCCESS);
					$this->_redirect('/');
				} else {
					$this->addFlashMessage('Niepoprawne dane logowania ', true, true);
					$this->log(Centixx_Log::LOGIN_FAILURE, "do konta " . $request->getParam('email'));
				}
			} else {
				$this->addFlashMessage('Nie podano wszystkich wymaganych danych', true, true);
			}
		}

		//dopisuje, bo flashMessanger normalnie dziala tylko przy przeladowaniu strony
		$this->view->messages += $this->_flashMessenger->getCurrentMessages();

		$this->view->loginForm = $loginForm;
	}

	public function loginAction()
	{
		$this->_forward('index');
	}

	public function logoutAction()
	{
		if (!Zend_Auth::getInstance()->hasIdentity()) {
			$this->_forward('index');
		} else if ($this->_getParam('action') == 'logout') {
			Zend_Auth::getInstance()->clearIdentity();
			$this->addFlashMessage("Wylogowałeś się!", false, true);
			$this->log(Centixx_Log::LOGIN_LOGOUT);
			$this->redirect('auth');
		}
	}

	/**
	 * Authenticate user
	 * @param Zend_Form $loginForm
	 * @return Zend_Auth_Result
	 */
	protected function _authenticate(Zend_Form $loginForm)
	{
		$auth = Zend_Auth::getInstance();

		$salt = $this->_config['security']['passwordSalt'];

		$adapter = new Zend_Auth_Adapter_DbTable($this->_db, 'users', 'user_email', 'user_password', "MD5(CONCAT('{$salt}', ?))");
		$adapter->setIdentity($loginForm->getValue('email'));
		$adapter->setCredential($loginForm->getValue('password'));

		return $auth->authenticate($adapter);
	}

	/**
	 * Przeładowuje obecnie zalogowanego użytkownika
	 */
	protected function _setCurrentUser($identify)
	{
		$this->_currentUser = Centixx_Model_Mapper_User::factory()->findByEmail($identify);
	}
}

