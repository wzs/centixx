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

    protected function _getPages()
    {
    	//TODO wczytać z configu
		return array(
		    array(
		        'label' => 'Centixx',
		    	'title' => 'Strona główna',
		        'controller' => 'index',
		    	'order'	=> -1,
		    ),
		    array(
		        'label' => 'wyloguj',
		    	'class' => 'logout',
		        'controller' => 'auth',
		    	'action'	=> 'logout',
		        'resource' => 'page-logout',
		    ),
		    array(
		        'label' => 'Pracownicy',
		        'controller' => 'users',
    	        'resource' => 'page-users',
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
		        'label' => 'Logowanie',
		        'controller' => 'auth',
		        'resource' => 'page-login',
		    ),
		    
		    array(
		        'label' => 'Raporty',
		        'controller' => 'reports',
		        'resource' => 'page-reports',
		    )

		);
    }
}