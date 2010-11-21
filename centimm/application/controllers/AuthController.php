<?php

class AuthController extends Centixx_Controller_Action
{
	public function indexAction()
	{
		if (Zend_Auth::getInstance()->getIdentity()) {
			$this->_forward('logout');
		}

		$db = $this->_getParam('db');
		$request = $this->getRequest();
		$loginForm = new Application_Form_Auth_Login();

		if ($request->isPost()) {
			if ($loginForm->isValid($request->getPost())) {
				$auth = $this->_authenticate($loginForm);
				if ($auth->isValid()) {
					$this->_setCurrentUser($auth->getIdentity());
					$this->_logger->log($this->_currentUser . " zalogował się", Centixx_Log::CENTIXX);
					$this->_flashMessenger->addMessage('Yeah! Zalogowany');
					$this->_redirect('/');
				} else {
					$this->_flashMessenger->addMessage('Niepoprawne dane logowania ' . join(' ', $auth->getMessages()));
				}
			} else {
				$this->_flashMessenger->addMessage('Popraw formularz');
			}
		}

		//dopisuje, bo flashMessanger normalnie dziala tylko przy przeladowaniu strony
		$this->view->messages += $this->_flashMessenger->getCurrentMessages();

		$this->view->loginForm = $loginForm;
	}

	public function logoutAction()
	{
		if (!Zend_Auth::getInstance()->hasIdentity()) {
			$this->_forward('index');
		} else if ($this->_getParam('action') == 'logout') {
			Zend_Auth::getInstance()->clearIdentity();
			$this->_flashMessenger->addMessage("Wylogowałeś się!");
			$this->_redirect('/');
		}
	}

	/**
	 * Authenticate user
	 * @param Zend_Form $loginForm
	 * @return Zend_Auth_Result
	 */
	protected function _authenticate(Zend_Form $loginForm)
	{
		debug($this->_config);

		$auth = Zend_Auth::getInstance();

		$salt = $this->_config['security']['passwordSalt'];

		$adapter = new Zend_Auth_Adapter_DbTable($this->_db, 'users', 'user_email', 'user_password', "MD5(CONCAT('{$salt}', ?))");
		$adapter->setIdentity($loginForm->getValue('email'));
		$adapter->setCredential($loginForm->getValue('password'));

		return $auth->authenticate($adapter);
	}

	/**
	 * Przeładowuje obecnie zalogowanego użytkownika ala ma kota
	 */
	protected function _setCurrentUser($identify)
	{
		$this->_currentUser = Centixx_Model_Mapper_User::factory()->findByEmail($identify);
	}
}

