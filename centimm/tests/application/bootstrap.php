<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	/**
	 * @var Zend_View_Abstract
	 */
	protected $_view = null;

	protected function _initDoctype()
	{
		$this->bootstrap('view');
		$this->_view = $this->getResource('view');
		$this->_view->doctype('XHTML1_STRICT');
	}
}