<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initView()
	{
		// Initialize view
		$view = new Zend_View();
		$view->doctype('XHTML1_STRICT');
		$view->headTitle('Centixx');
		$view->headLink()->appendStylesheet('/styles/basic.css');

		// Add it to the ViewRenderer
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
		$viewRenderer->setView($view);

		//Pass the flash messages to the main layout
		$view->messages = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger')->getMessages();

		// Return it, so that it can be stored by the bootstrap
		return $view;
	}

	protected function _initSession()
	{
		Zend_Session::start();
	}

	protected function _initNamespaces()
	{
		Zend_Loader_Autoloader::getInstance()->registerNamespace('Centixx_');
	}

	protected function _initDatabase()
	{
		$db = $this->bootstrap('db')->getResource('db');
		Zend_Registry::getInstance()->set('db', $db);

        $profiler = new Zend_Db_Profiler_Firebug('All DB Queries');
        $profiler->setEnabled(true);
        $db->setProfiler($profiler);

		return $db;
	}

	protected function _initCurrentUser()
	{
		$auth = Zend_Auth::getInstance();
		$user = null;
		if ($auth->hasIdentity()) {
			$userMapper = new Centixx_Model_Mapper_User();
			$user = $userMapper->findByField($auth->getIdentity(), 'user_email');
		}
		Zend_Registry::getInstance()->set('currentUser', $user);
		return $user;
	}

	protected function _initAcl()
	{
		//TODO wczytywanie uprawnien z xml'owego configu
		$acl = new Zend_Acl();
		Zend_Registry::getInstance()->set('acl', $acl);
		return $acl;
	}

	protected function _initConfig()
	{
		$config = $this->getOptions();
	    Zend_Registry::set('config', $config);
	    return $config;
	}

	protected function _initLog()
	{
		$this->bootstrap('config');
		$e = Zend_Registry::getInstance()->get('config');
		$log = Zend_Log::factory(array($e['resources']['log']));

		//ustawiam logowanie tylko zdarzen specyficznych dla aplikacji
		$log->addPriority('Centixx', Centixx_Log::CENTIXX);
		$log->addFilter(new Zend_Log_Filter_Priority(Centixx_Log::CENTIXX, '='));

		Zend_Registry::getInstance()->set('log', $log);

		return $log;
	}
}

