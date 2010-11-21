<?php
class Centixx_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
	protected $_acl = null;
	protected $_currentUser = null;

	public function __construct($currentUser, $acl) {
		$this->_currentUser = $currentUser;
		$this->_acl = $acl;
	}

    /**
     * (non-PHPdoc)
     * @see library/Zend/Controller/Plugin/Zend_Controller_Plugin_Abstract::preDispatch()
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
		$resource = $request->getControllerName()
			. Centixx_Acl_ActionResource::SEPARATOR
			. $request->getActionName();

		if (!$this->_acl->isAllowed($this->_currentUser, $resource)) {
			throw new Centixx_Acl_AuthenticationException('Nieautoryzowany dostęp do strony');
		}
    }
}