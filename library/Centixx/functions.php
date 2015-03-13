<?php
/**
 * Plik zawiera króŧkie funkcje - aliasy do statycznych metod ZF
 */
/**
 * Merges any number of arrays / parameters recursively, replacing
 * entries with string keys with values from latter arrays.
 * If the entry or the next value to be assigned is an array, then it
 * automagically treats both arguments as an array.
 * Numeric entries are appended, not replaced, but only if they are
 * unique
 *
 * calling: result = array_merge_recursive_distinct(a1, a2, ... aN)
 **/

function array_merge_recursive_distinct () {
	$arrays = func_get_args();
	$base = array_shift($arrays);
	if(!is_array($base)) $base = empty($base) ? array() : array($base);
	foreach($arrays as $append) {
		if(!is_array($append)) $append = array($append);
		foreach($append as $key => $value) {
			if(!array_key_exists($key, $base) and !is_numeric($key)) {
				$base[$key] = $append[$key];
				continue;
			}
			if(is_array($value) or is_array($base[$key])) {
				$base[$key] = array_merge_recursive_distinct($base[$key], $append[$key]);
			} else if(is_numeric($key)) {
				if(!in_array($value, $base)) $base[] = $value;
			} else {
				$base[$key] = $value;
			}
		}
	}
	return $base;
}

/**
 * Zapisuje komunikat diagnostyczny do FirePHP
 * @param string $msg
 * @param int $level
 */
function debug($msg, $level = null) {
	if ($level == null) {
		$level = Zend_Log::DEBUG;
	}

	$logger = Zend_Registry::get('firephplog');
	if ($logger) {
		$logger->log($msg, $level);
	}
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

	if (!$uppercaseFirst) {
		$t[0] = strtolower($t[0]);
	}

	return implode('', $t);

}

function write($array) {
	$pos = array(
	array(25, 111),
	array(62, 111),
	array(100, 158),
	array(139, 112),
	array(139, 453),
	array(177, 112),
	array(217, 111),
	array(254, 111),
	array(294, 111),
	array(331, 111)
	);

	$img = imagecreatefromjpeg(APPLICATION_PATH . '/../public/img/blankiet.jpg');
	$black = imagecolorallocate($img, 0, 0, 0);

	foreach ($array as $key => $val) {
		for ($i=0; $i < strlen($val); $i++) {
			if ($key != 5) {
				imagestring($img, 5, $pos[$key][1]+$i*22.8, $pos[$key][0], strtoupper($val[$i]), $black);
			}else{
				imagestring($img, 5, $pos[$key][1]+$i*8, $pos[$key][0], strtolower($val[$i]), $black);
			}
		}
	}

	return $img;
}

function getMonthName($monthNumber){
	$name;
	switch ($monthNumber) {
		case 1:
			$name = 'Styczeń';
			break;

		case 2:
			$name = 'Luty';
			break;

		case 3:
			$name = 'Marzec';
			break;

		case 4:
			$name = 'Kwiecień';
			break;

		case 5:
			$name = 'Maj';
			break;

		case 6:
			$name = 'Czerwiec';
			break;

		case 7:
			$name = 'Lipiec';
			break;

		case 8:
			$name = 'Sierpień';
			break;

		case 9:
			$name = 'Wrzesień';
			break;

		case 10:
			$name = 'Październik';
			break;

		case 11:
			$name = 'Listopad';
			break;

		case 12:
			$name = 'Grudzień';
			break;

		default:
			$name = 'MIESIĄC';
			break;
	}

	return $name;
}

function getTextAmount($amount){
	$array = explode('.', $amount);
	$zl = $array[0];
	$gr = $array[1];
	$zl = getTextFromNumber($zl);
	$gr = getTextFromNumber($gr);
	$textAmmount = $zl;
	$textAmmount .= "zlotych i ";
	$textAmmount .= ($gr != '') ? $gr : "zero ";
	$textAmmount .= "groszy";
	return $textAmmount;
}

function getTextFromNumber($ammount){

	$numbers = array(
	array('', '', '', ''),
	array('jeden ', 'jedenascie ', 'dziesiec ', 'sto '),
	array('dwa ', 'dwanascie ', 'dwadziescia ', 'dwiescie '),
	array('trzy ', 'trzynascie ', 'trzydziesci ', 'trzysta '),
	array('cztery ', 'czternascie ', 'czterdziesci ', 'czterysta '),
	array('piec ', 'pietnascie ', 'piecdziesiat ', 'piecset '),
	array('szesc ', 'szesnascie ', 'szescdziesiat ', 'szescset '),
	array('siedem ', 'siedemnascie ', 'siedemdziesiat ', 'siedemset '),
	array('osiem ', 'osiemnascie ', 'osiemdziesiat ', 'osiemset '),
	array('dziewiec ','dziewietnascie ','dziewiecdziesiat ','dziewiecset '));
	$groups = array(
	array('' ,'' ,''),
	array('tysiac ' ,'tysiace ' ,'tysiecy '),
	array('milion ' ,'miliony ' ,'milionow '),
	array('miliard ','miliardy ','miliardow '),
	array('bilion ' ,'biliony ' ,'bilionow '),
	array('biliard ','biliardy ','biliardow '),
	array('trylion ','tryliony ','tryliardow '));

	$J;
	$N;
	$D;
	$S;
	$G;
	$K;
	$Znak;

	$G = 0;
	if ($ammount<0) $Znak = 'minus ';
	else $Znak = '';
	if ($ammount==0) $Slownie = 'zero';
	else{
		do{
			$S=(abs($ammount) % 1000) /100;
			$D=(abs($ammount) % 100) /10;
			$J=abs($ammount) % 10;
			if (($D == 1) && ($J > 0)){
				$N = $J;
				$D = 0;
				$J = 0;
			}
			else $N = 0;
			switch ($J) {
				case 1:
					//                		if ($S + $D + $N > 0){
					//                			$K = 2;
					//                		}
					//                		else $K = 0;
					$K = 0;
					break;

				case 2:
					$K = 1;
					break;

				case 3:
					$K = 1;
					break;

				case 4:
					$K = 1;
					break;

				default:
					$K = 2;
					break;
			}

			if ($S + $D + $N + $J > 0){
				$Result = $numbers[$S][3].$numbers[$D][2].$numbers[$N][1].$numbers[$J][0].$groups[$G][$K].$Result;
			}
			$ammount = $ammount/1000;
			$G = $G + 1;
		}while($ammount!=0);
	}
	$Result = $Znak.$Result;
	return $Result;
}

/**
 * Zmienia polskie znaki diaktryczne
 * @param string $s
 * @return string
 */
function strip_pl($s)
{
	$s = strtr($s, array(
	// UTF-8
    'Ą' => 'A',
    'Ć' => 'C',
    'Ę' => 'E',
    'Ł' => 'L',
    'Ń' => 'N',
    'Ó' => 'O',
    'Ś' => 'S',
    'Ź' => 'Z',
    'Ż' => 'Z',
    'ą' => 'a',
    'ć' => 'c',
    'ę' => 'e',
    'ł' => 'l',
    'ń' => 'n',
    'ó' => 'o',
    'ś' => 's',
    'ź' => 'z',
    'ż' => 'z'
    ));

    return $s;
}