<?php
/**
 * Reprezentuje menu główne aplikacji.
 * Automatycznie uwzględnia ustawiena z ActionAcl'a,
 * o ile poprawnie zostaną zdefiniowane klucze resource
 * @author wzs
 *
 */
class Centixx_Navigation extends Zend_Navigation
{

    public function __construct()
    {
    	$this->addPages($this->_getPages());
    }

    public function getActive()
    {
    	foreach ($this->_pages as $p) {
    		if ($p->isActive()) {
    			return $p;
    		}
    	}
    }

    protected function _getPages()
    {
    	//TODO wczytać z configu
		return array(
			array(
		        'controller' => 'index',
		        'resource' => 'page-index',
				'visible' => false,
		    ),

		    array(
		        'controller' => 'auth',
		        'resource' => 'page-auth',
				'visible' => false,
		    ),

			array(
		        'label' => 'Grupy',
		        'controller' => 'groups',
		        'resource' => 'page-groups',
		    ),

			array(
		        'label' => 'Projekty',
		        'controller' => 'projects',
		        'resource' => 'page-projects',
		    ),

			array(
		        'label' => 'Panel Administracyjny',
		        'controller' => 'admin',
		        'resource' => 'page-admin',
		    ),

			array(
		        'label' => 'Czas pracy',
		        'controller' => 'timesheet',
		        'resource' => 'page-timesheet',
		    ),

		    array(
		        'label' => 'Uprawnienia',
		        'controller' => 'permissions',
		        'resource' => 'page-permissions',
		    ),

		    array(
		        'label' => 'Moduł kadrowy',
		        'controller' => 'users',
		        'resource' => 'page-users',
		    ),

		    array(
		        'label' => 'Moduł księgowy',
		        'controller' => 'accounting',
		        'resource' => 'page-accounting',
		    ),

		    array(
		        'label' => 'Raporty',
		        'controller' => 'reports',
		        'resource' => 'page-reports',
		    )

		);
    }
}