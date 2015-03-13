<?php
/**
 * Compare two dates
 * @author wzs
 */
class Centixx_Validate_CompareDates extends Zend_Validate_Abstract
{
	/**
	 * Method isValid($value) will return true, if comparedDate is equal to the $value
	 * @var int
	 */
	const COMPARE_TYPE_EQUAL 	= 0;

	/**
	 * Method isValid($value) will return true, if comparedDate is later then $value
	 * @var int
	 */
	const COMPARE_TYPE_LATER 	= 1;

	/**
	 * Method isValid($value) will return true, if comparedDate is earlier then $value
	 * @var int
	 */
	const COMPARE_TYPE_EARLIER 	= -1;

    const INVALID        = 'comparisionInvalid';
    protected $_messageTemplates = array(
        self::INVALID        => "Comparision failed",
    );

	protected $_comparedDate;
	protected $_compareType;

    /**
     * Sets validator options
     * Accepts the following option keys:
     *   'compareType' => string|Zend_Date
     *   'comparedDate' => int,
     *
     * @param  array|Zend_Config $options
     * @return void
     */
    public function __construct($options)
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        }

        if (!array_key_exists('comparedDate', $options)) {
            require_once 'Zend/Validate/Exception.php';
            throw new Zend_Validate_Exception("Missing option. 'comparedDate' has to be given");
        }

        if (!isset($options['compareType'])) {
			$options['compareType'] = self::COMPARE_TYPE_GREATER;
        }

        $this
        	->setComparedDate($options['comparedDate'])
        	->setCompareType($options['compareType'])
        ;
    }


    public function setCompareType($type)
    {
    	if (!in_array($type, range(-1,1))) {
    		throw new Zend_Validate_Exception("Cannot set compare type to $type Try to use predefinied constans");
    	}
    	$this->_compareType = $type;
    	return $this;
    }

    public function getCompareType()
    {
    	return $this->_compareType;
    }

	public function setComparedDate($date)
	{
		if (!$date instanceof Zend_Date) {
			$tmp = explode('-', $date);
			$date = new Zend_Date(array('year' => $tmp[0], 'month' => $tmp[1], 'day' => $tmp[2]));
		}
		$this->_comparedDate = $date;
		return $this;
	}

	public function getComparedDate($raw = false)
	{
		return $raw ? $this->_comparedDate : $this->_comparedDate? $this->_comparedDate->toString($this->_dateFormat) : null;
	}

	public function isValid($date)
	{
		if (!$date instanceof Zend_Date) {
			$tmp = explode('-', $date);
			$date = new Zend_Date(array('year' => $tmp[0], 'month' => $tmp[1], 'day' => $tmp[2]));
		}
		if ($date->compareDate($this->_comparedDate) !== $this->_compareType) {
			$this->_error(self::INVALID);
			return false;
		}

		return true;
	}
}