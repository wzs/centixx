<?php
class Zend_View_Helper_ProfileLink extends Zend_View_Helper_Abstract
{
	public $view;

	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
	}

	public function profileLink()
	{
		if (Zend_auth::getInstance()->hasIdentity()) {
			return
			  '<a href="' . $this->view->url(array('controller' => 'auth',  'action' => 'logout')) . '">Wyloguj się</a>';
		}

		return '<a href="' . $this->view->url(array('controller'=>'auth'), 'default', true) . '">Zaloguj się</a>';
	}
}