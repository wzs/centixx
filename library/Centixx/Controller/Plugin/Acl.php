<?php
/**
 * Plugin do Controllerów, który przed właściwym wywołaniem konkretnej akcji sprawdza,
 * czy obecnie zalogowany użytkownik ma do niej dostęp (na podstawie ACL'a ActionResource)
 * @author wzs
 *
 */
class Centixx_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
	/**
	 * @var Zend_Acl
	 */
	protected $_acl = null;

	/**
	 * @var Centixx_Acl
	 */
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

		$resource = 'page-' . $request->getControllerName();

		//jesli nie zdefiniowano w ACL'u reguł dostępu do kontrolera, domyślnie dostep jest możliwy
		$denyAccess = false;

		if ($this->_acl->has($resource)) {
			$denyAccess = true;

			if ($this->_currentUser) {
				//sprawdzam czy którakolwiek z ról dostepnych dla uzytkownika daje dostep do strony
				foreach ($this->_currentUser->getRoles() as $role) {
					if ($this->_acl->isAllowed($role->id, $resource)) {
						$denyAccess = false;
						break;
					}
				}
			} else {
				if ($this->_acl->isAllowed($this->_currentUser, $resource)) {
					$denyAccess = false;
				}
			}
		}


		if ($denyAccess) {
			$this->getRequest()->setControllerName('error');
			throw new Centixx_Acl_AuthenticationException('Nieautoryzowany dostęp do strony');
		}
    }
}
