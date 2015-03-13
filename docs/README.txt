Przydante info

Sporo rzeczy jest dostepna przez globalny rejestr Zend_Registry::getInstance() 


Logowanie akcji systemowych:
	w kontrolerze: $this->_logger->log("Treść", Centixx_Log::Centixx);
	(jeśli trzeba gdzieś indziej: Zend_Registry::getInstance()->get('log')->log("Treść", Centixx_Log::Centixx))
	
Dostęp do obiektu obecnie zalogowanego użytkownika
	w kontrolerze: $this->_currentUser
	gdzie indziej: Zend_Registry::getInstance()->get('currentUser')
	
Dostęp do bazy
	jeśli nia ma jakoś inaczej dostepu, to $db = Zend_Registry::getInstance()->get('db')
	jeśli chcemy mieć dostęp do PDO (odpowiednik JDBC - patrz manual.php.net) trzeba $pdo = $db->getDefaultAdapter()