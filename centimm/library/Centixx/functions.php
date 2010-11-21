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