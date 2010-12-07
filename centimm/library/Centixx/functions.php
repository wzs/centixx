<?php
/**
 * Plik zawiera króŧkie funkcje - aliasy do statycznych metod ZF
 */

/**
 * Zapisuje komunikat diagnostyczny do FirePHP
 * @param string $msg
 * @param int $level
 */
function debug($msg, $level = null) {
	if ($level == null) {
		$level = Zend_Log::DEBUG;
	}
	Zend_Registry::get('firephplog')->log($msg, $level);
}

/**
 * Zwraca n-ty element tablicy
 * @param array $array
 * @param int $n indeks zwracanego elementu w tablicy
 * @param mixed
 */
function array_get($array, $n) {
	return $array[$n];
};

/**
 * Zmienia podaną nazwę na format CamelCase: usuwane są znaki podkreślenia
 * i każda ze składowych cześci rozpoczyna się wielkją literą
 * @example hello_world -> helloWorld
 * 
 * @param string $text
 * @param bool $uppercaseFirst czy pierwsza litera ma być wielką literą
 * @return striong 
 */
function camelCase($text, $uppercaseFirst = false)
{
	$t = explode('_', $text);
	foreach ($t as &$part) {
		$part = ucfirst($part);
	}
	
	if ($uppercaseFirst) {
		$t[0] = ucfirst($t[0]);
	}
	return implode('', $t);
	
}