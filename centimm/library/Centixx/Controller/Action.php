<?php
abstract class Centixx_Controller_Action extends Zend_Controller_Action
{
	/**
	 * @var Zend_Db_Adapter_Abstract
	 */
	protected $_db = null;

	/**
	 * @var Zend_Config
	 */
	protected $_config = null;

	/**
	 * @var Zend_Controller_Action_Helper_FlashMessenger
	 */
	protected $_flashMessenger;

	/**
	 * Jeśli użytkownik jest zalogowany,
	 * to pole to zawiera obiekt go reprezentujący
	 * @var Centixx_Model_User
	 */
	protected $_currentUser = null;

	/**
	 * @var Centixx_Log
	 */
	protected $_logger = null;

	/**
	 * @var bool czy request jest AJAXowy
	 */
	protected $_isAjaxRequest;

	public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
	{
		$registry = Zend_Registry::getInstance();
		parent::__construct($request, $response, $invokeArgs);
		$this->_db = $registry->get('db');
		$this->_logger = $registry->get('centixx_logger');
		$this->_currentUser = $registry->get('currentUser');
		$this->_flashMessenger = $this->getHelper('FlashMessenger');
		$this->_config = Zend_Registry::get('config');

		$this->view->currentUser = $this->_currentUser;
		$this->view->messages += $this->_flashMessenger->getMessages();

		$this->_isAjaxRequest = $this->getRequest()->isXmlHttpRequest();
		if ($this->_isAjaxRequest) {
			$this->_helper->layout()->disableLayout();
	        $this->_helper->viewRenderer->setNoRender();
		}
	}

	/**
	 * Załącza plik bądz pliki JS
	 * @param string|array<string> $scripts ścieżka (bądź tablica ścieżek) do skryptu - wzglądna wobec ścieżki głównej do aplikacji
	 *  np. 'js/jquery.js'
	 */
	protected function appendScript($scripts)
	{
		if (!is_array($scripts)) {
			$scripts = array($scripts);
		}

		foreach ($scripts as $script) {
			$this->view->headScript()->appendFile(
				$this->_config['resources']['layout']['basePath'] . '/' . $script,
	    		'text/javascript'
	    		);
		}
	}

	/**
	 * Załącza plik bądz pliki CSS
	 * @param string|array<string> $styles ścieżka (bądź tablica ścieżek) do pliku css - wzglądna wobec ścieżki głównej do aplikacji
	 *  np. 'css/basic.css'
	 */
	protected function appendStyles($styles)
	{
		if (!is_array($styles)) {
			$styles = array($styles);
		}
		foreach ($styles as $style) {
			$this->view->headLink()->appendStylesheet(
				$this->_config['resources']['layout']['basePath'] . '/' . $style
			);
		}

	}


	/**
	 * Loguje specyficzne dla aplikacji wydarzenia
	 * @param string $message treść wiadomości kontekst użytkownika wykonujacego akcje zostanie automatycznie dodany
	 * @param int $type typ logowanej wiadomości (patrz zdefiniowane stałe w Centixx_Log)
	 */
	protected function log($type, $message = null)
	{
		if (!$this->_logger) {
			return;
		}

		$this->_logger->log($type, $message, $this->_currentUser);
	}
}