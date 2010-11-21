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