<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initView()
	{

		$view = new Zend_View();
		$view->doctype(Zend_View_Helper_Doctype::HTML4_STRICT);
		$view->headTitle('Centixx');

		$this->bootstrap('layout');
		$cfg = $this->getOption('resources');

		$layout = $this->getResource('layout');
		$view->basePath = $layout->basePath = $cfg['layout']['basePath'];

		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
		$viewRenderer->setView($view);

		//dołączamy globalnie jquery
		$view->headScript()->appendFile(
    		$cfg['layout']['basePath'] . '/js/jquery.js',
    		'text/javascript'
		);

		$view->headScript()->appendFile(
    		$cfg['layout']['basePath'] . '/js/jquery-ui.min.js',
    		'text/javascript'
		);

		$view->headScript()->appendFile(
    		$cfg['layout']['basePath'] . '/js/main.js',
    		'text/javascript'
		);

		$view->headLink()
			->appendStylesheet($cfg['layout']['basePath'] . '/styles/basic.css')
			->appendStylesheet('http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.7/themes/base/jquery-ui.css')
		;

		$view->messages = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger')->getMessages();

//		$view->addHelperPath("ZendX/JQuery/View/Helper", "ZendX_JQuery_View_Helper");

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
		$this->bootstrap('currentUser');
		$currentUser = $this->getResource('currentUser');

		$modelResourceAcl = new Centixx_Acl_ModelResource();
		Zend_Registry::set('Zend_Acl', $modelResourceAcl);

		//rejestrowanie pluginu, w którym automatycznie sprawdzane są uprawnienia
		//do wykonania akcji w kontrolerze

//		$actionResourceAcl = new Centixx_Acl_ActionResource();
//		Zend_Registry::set('actionAcl', $actionResourceAcl);
		$frontController = Zend_Controller_Front::getInstance();
		$frontController->registerPlugin(new Centixx_Controller_Plugin_Acl($currentUser, $modelResourceAcl));

		return $modelResourceAcl;
	}

	protected function _initConfig()
	{
		$config = $this->getOptions();
		Zend_Registry::set('config', $config);
		return $config;
	}

	protected function _initNavigation()
	{
		$this->bootstrap('acl');
		$acl = $this->getResource('acl');

		$this->bootstrap('currentUser');
		$currentUser = $this->getResource('currentUser');

		$navi = new Centixx_Navigation();
		Zend_Registry::set('Zend_Navigation', $navi);

		//ustawiam ACL'a używanego przez menu
		Zend_View_Helper_Navigation_Menu::setDefaultAcl($acl);
		Zend_View_Helper_Navigation_Menu::setDefaultRole($currentUser);

		return $navi;
	}


	protected function _initLog()
	{
		$this->bootstrap('config');
		$config = Zend_Registry::getInstance()->get('config');


		$writer = Zend_Log_Writer_Stream::factory($config['resources']['log']['writerParams']);
		$writer->setFormatter(new Zend_Log_Formatter_Simple("%timestamp%\t%priority%\t%message%" . PHP_EOL));

		$log = new Centixx_Log();
		$log->addWriter($writer);

		Zend_Registry::set('centixx_logger', $log);

		//dodatkowo do debugowania przez FirePHP
		$firePhpLog = new Zend_Log(new Zend_Log_Writer_Firebug());
		Zend_Registry::set('firephplog', $firePhpLog);

		return $log;
	}

	protected function _initCache()
	{

		$frontendOptions = array('automatic_serialization' => true);
		$backendOptions  = array('cache_dir' => APPLICATION_PATH . '/../data/cache');

		//cache dla schematów bazy danych
		$cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
		Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);

		return $cache;
	}

	protected function _initLocale()
	{
		$this->bootstrap('config');
		$config = Zend_Registry::getInstance()->get('config');

		date_default_timezone_set($config['locale']['timezone']);
		$locale = new Zend_Locale($config['locale']['locale']);

		Zend_Registry::set('Zend_Locale', $locale);
		return $locale;
	}
}

