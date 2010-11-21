<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initView()
	{

		// Initialize view
		$view = new Zend_View();
		$view->doctype(Zend_View_Helper_Doctype::HTML4_STRICT);
		$view->headTitle('Centixx');

		//set basePath
		$this->bootstrap('layout');
		$cfg = $this->getOption('resources');

		$layout = $this->getResource('layout');
		$layout->basePath = $cfg['layout']['basePath'];

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
		$autoloader = Zend_Loader_Autoloader::getInstance();
		$autoloader->registerNamespace('Centixx_');
	}

	protected function _initDatabase()
	{
		$db = $this->bootstrap('db')->getResource('db');
		$db->setFetchMode(Zend_Db::FETCH_OBJ);
		Zend_Registry::set('db', $db);

		return $db;
	}

	protected function _initCurrentUser()
	{
		$auth = Zend_Auth::getInstance();
		$user = null;
		if ($auth->hasIdentity()) {
			$user = Centixx_Model_Mapper_User::factory()->findByEmail($auth->getIdentity());
		}
		Zend_Registry::set('currentUser', $user);
		return $user;
	}

	protected function _initAcl()
	{
		$currentUser = $this->bootstrap('currentUser')->getResource('currentUser');
		if ($currentUser == null) {
		}

		$actionResourceAcl = new Centixx_Acl_ActionResource();
		Zend_Registry::set('actionAcl', $actionResourceAcl);

		$modelResourceAcl = new Centixx_Acl_ModelResource();
		Zend_Registry::set('modelAcl', $modelResourceAcl);

		$frontController = Zend_Controller_Front::getInstance();
		$frontController->registerPlugin(new Centixx_Controller_Plugin_Acl($currentUser, $actionResourceAcl));

		return array('action' => $actionResourceAcl, 'model' => $modelResourceAcl);
	}

	protected function _initConfig()
	{
		$config = $this->getOptions();
		Zend_Registry::set('config', $config);
		return $config;
	}

	protected function _initNavigation()
	{
		$acl = $this->bootstrap('acl')->getResource('acl');
		$currentUser = $this->bootstrap('currentUser')->getResource('currentUser');

		$navi = new Centixx_Navigation();
		Zend_Registry::set('Zend_Navigation', $navi);

		Zend_View_Helper_Navigation_Menu::setDefaultAcl($acl['action']);
		Zend_View_Helper_Navigation_Menu::setDefaultRole($currentUser);

		return $navi;
	}


	protected function _initLog()
	{
		$this->bootstrap('config');
		$config = Zend_Registry::getInstance()->get('config');
		$log = Zend_Log::factory(array($config['resources']['log']));

		//ustawiam logowanie tylko zdarzen specyficznych dla aplikacji
		$log->addPriority('Centixx', Centixx_Log::CENTIXX);
		$log->addFilter(new Zend_Log_Filter_Priority(Centixx_Log::CENTIXX, '='));

		Zend_Registry::set('log', $log);

		//dodatkowo do debugowania przez FirePHP
		$firePhpLog = new Zend_Log(new Zend_Log_Writer_Firebug());
		Zend_Registry::set('firephplog', $firePhpLog);

		return $log;
	}

	protected function _initCache()
	{

		$frontendOptions = array('automatic_serialization' => true);
		$backendOptions  = array('cache_dir' => APPLICATION_PATH . '/../data/cache');
		$cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);

		Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);

		return $cache;
	}
}

