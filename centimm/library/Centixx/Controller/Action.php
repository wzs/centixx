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
		parent::__construct($request, $response, $invokeArgs);
		$registry = Zend_Registry::getInstance();
		$this->_config 		= $registry->get('config');
		$this->_db 			= $registry->get('db');
		$this->_logger 		= $registry->get('centixx_logger');
		$this->_currentUser = $registry->get('currentUser');

		$this->_flashMessenger = $this->getHelper('FlashMessenger');

		$this->view->currentUser = $this->_currentUser;
		$this->view->messages = $this->_flashMessenger->getMessages();

		//ustawienie id strony
		$activePage = $this->view->navigation()->getContainer()->getActive();
		$this->view->pageId = $activePage ? $activePage->getResource() : '';

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

	/**
	 * Dodaje "wiadomość błyskawiczną", np. rezultat wykonanej akcji
	 * @param string $text tresc
	 * @param bool $negativeResult czy akcja zakonczyla sie niepowoedzeniem (zostanie oznaczona na czerwono)
	 * @param bool $fading czy wiadomość ma być animowana (zostanie ukryta kilka sekund po wyswietleniu)
	 */
	protected function addFlashMessage($text, $negativeResult = false, $fading = false)
	{
		$this->view->messages[] = array('text' => $text, 'status' => $negativeResult ? 'error' : 'success', 'fading' => $fading);
	}

    protected function _redirect($url, array $options = array())
    {
    	//zapisuje wiadomosci blyskawiczne, aby pokazac je na nowej stronie
    	if ($this->view->messages) {
			foreach ($this->view->messages as $msg) {
				$this->_flashMessenger->addMessage($msg);
			}
    	}

        parent::_redirect($url, $options);
    }

    /**
     *
     * Wykonuje przekierowanie na wskazaną strone mvc
     * @param string $controller
     * @param string $action
     * @param array $params
     */
    protected function redirect($controller, $action = null, $params = array())
    {
    	$options = array(
    		'controller' => $controller,
    		'action' => $actionName,
    	);
		$options += $params;
    	$url = Zend_View_Helper_Url::url($options);

    	$this->_redirect($url);
    }
}