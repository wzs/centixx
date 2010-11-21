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
		        'label' => '¢entixx',
		    	'title' => 'Strona główna',
		        'controller' => 'index',
		    	'order'	=> -1,
		    ),
		    array(
		        'label' => 'wyloguj',
		    	'class' => 'logout',
		        'controller' => 'auth',
		    	'action'	=> 'logout',
		        'resource' => 'auth:logout',
		    ),
		    array(
		        'label' => 'Users',
		        'controller' => 'users',
    	        'resource' => 'users:index',
		    ),

		    array(
		        'label' => 'Grupy',
		        'controller' => 'groups',
		        'resource' => 'groups:index',
		    ),

		    array(
		        'label' => 'Logowanie',
		        'controller' => 'auth',
		        'resource' => 'auth:index',
		    ),

		);
    }
}