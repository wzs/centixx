<?php
interface Centixx_Model_Interface extends Zend_Acl_Resource_Interface
{
    /**
     * Sprawdza, czy podana rola ma dostep do tego obiektu
     * z uwzglednieniem globalnej ACL jak i specyficznych warunków
     *
     * @param Zend_Acl_Role_Interface $role
     * @param string|null $privilege
     * @param Zend_Acl|null $acl
     * @return bool
     */
	public function isAllowed($role, $privilege = null, Zend_Acl $acl = null);
}