<?php
abstract class Centixx_Controller_Action extends Zend_Controller_Action
{
	/**
	 * @var Zend_Db
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
	 * @var Zend_Log
	 */
	protected $_logger = null;

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
    	$registry = Zend_Registry::getInstance();
        parent::__construct($request, $response, $invokeArgs);
		$this->_db = $registry->get('db');
		$this->_logger = $registry->get('log');
		$this->_currentUser = $registry->get('currentUser');
		$this->_flashMessenger = $this->getHelper('FlashMessenger');
    }
}