<?php

/**
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license
 * It is  available through the world-wide-web at this URL:
 * http://www.petala-azul.com/bsd.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to geral@petala-azul.com so we can send you a copy immediately.
 *
 * @package    Bvb_Grid
 * @copyright  Copyright (c)  (http://www.petala-azul.com)
 * @license    http://www.petala-azul.com/bsd.txt   New BSD License
 * @version    $Id: Date.php 1072 2010-03-19 21:33:15Z bento.vilas.boas@gmail.com $
 * @author     Bento Vilas Boas <geral@petala-azul.com >
 */

class Bvb_Grid_Formatter_Date implements Bvb_Grid_Formatter_FormatterInterface
{

    protected $locale = null;

    protected $date_format = null;

    protected $type = null;

    public function __construct ($options = array())
    {
        if ($options instanceof Zend_Locale) {
            $this->locale = $options;
        } elseif (is_string($options)) {
            $this->date_format = $options;
        } elseif (is_array($options)) {
            foreach ($options as $k => $v) {
                switch ($k) {
                    case 'locale':
                        $this->locale = $v;
                        break;
                    case 'date_format':
                        $this->date_format = $v;
                        break;
                    case 'type':
                        $this->type = $v;
                        break;
                    default:
                        throw new Bvb_Grid_Exception($this->__("Unknown option '$k'."));
                }
            }
        } elseif (Zend_Registry::isRegistered('Zend_Locale')) {
            $this->locale = Zend_Registry::get('Zend_Locale');
        }
    }

    public function format ($value)
    {
        try {
            $date = new Zend_Date($value);
        }
        catch (Exception $e) {
            return $value;
        }
        return $date->toString($this->date_format, $this->type, $this->locale);
    }

}