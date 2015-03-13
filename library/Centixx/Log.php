<?php
class Centixx_Log  extends Zend_Log
{
	//stałę definiujące rodzaje logowanych akcji
	const LOGIN_SUCCESS 		= 9;
	const LOGIN_FAILURE 		= 10;
	const LOGIN_LOGOUT 			= 11;
	const UNAUTHORISED_ACCESS 	= 12;
	const DB_COPY_CREATED 		= 13;
	const PERMISSION_GRANTED 	= 14;
	const USER_CREATED			= 15;
	const USER_UPDATED 			= 16;
	const USER_DELETED 			= 17;
	const LOG_CLEARED			= 18;

	protected static $actionTypeNames = array(
		9 => 'Logowanie',
		'Nieudane logowanie',
		'Wylogowanie',
		'Nieuprawniony dostęp',
		'Utworzenie kopii zapasowej bazy danych',
		'Cesja uprawnień',
		'Utworzenie użytkownika',
		'Edycja danych użytkownika',
		'Usunięcie użytkownika',
		'Wyczyszczenie logów systemowych',
	);

	/**
	 * Znak separatora w logu
	 * @var string
	 */
	public static $logLineSeparator = "\t";

	/**
	 * Loguje specyficzne dla aplikacji wydarzenia
	 * @param string $message treść wiadomości kontekst użytkownika wykonujacego akcje zostanie automatycznie dodany
	 * @param int $type typ logowanej wiadomości (patrz zdefiniowane stałe)
	 * @param Centixx_User_Model|null $user kontekst użytkownika - jesli nie podany, zostanie uzyty kontekst obecnie zalogowanego uzytkownika
	 */
	public function log($type, $message = "", $user = null)
	{
		if ($user instanceof Centixx_Model_User) {
			$user = (string)$user;
		} else {
			$user = $_SERVER['REMOTE_ADDR'];
		}

		$newMessage = join(self::$logLineSeparator, array($user, $message));
		parent::log($newMessage, $type, null);
	}

	/**
	 * Parsuje pojedyńczą linię loga
	 * @param string $line
	 * @return array
	 */
	public static function parseLog($line)
	{
		$t = explode(self::$logLineSeparator, trim($line));
		return array(
			'date' 			=> new Zend_Date($t[0]),
			'actionNo' 		=> $t[1],
			'actionName' 	=> self::getActionName($t[1]),
			'user' 			=> $t[2],
			'message' 		=> $t[3],
		);
	}

	/**
	 * Zwraca opisową nazwę podanego typu akcji
	 * @param int $no stała zdefiniowana w tej klase
	 */
	protected function getActionName($no)
	{
		return self::$actionTypeNames[$no];
	}

}