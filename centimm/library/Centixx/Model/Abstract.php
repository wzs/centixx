<?php
abstract class Centixx_Model_Abstract
{
	const CLASS_PATH_SEPARATOR = '_';
	const CLASS_PREFIX = 'Centixx_Model_';

	/**
	 * @var Centixx_Model_Mapper_Abstract
	 */
	protected $_mapper = null;

	public function setMapper(Centixx_Model_Mapper_Abstract $mapper)
	{
		$this->_mapper = $mapper;
	}

    public function __construct(array $options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }

    public function __set($name, $value)
    {
        $method = 'set' . $name;
        if (!method_exists($this, $method)) {
            throw new Centixx_Model_Exception('Invalid property');
        }
        $this->$method($value);
    }

    public function __get($name)
    {
        $method = 'get' . $name;
        if (!method_exists($this, $method)) {
            throw new Centixx_Model_Exception('Invalid property');
        }
        return $this->$method();
    }

    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }

	public function setId($id)
	{
		$validator = new Zend_Validate_Int();
		if ($validator->isValid($id)) {
			$this->_id = $id;
		} else {
			throw new Centixx_Model_Exception("Invalid property for id");
		}
		return $this;
	}

	public function getId()
	{
		return $this->_id;
	}

	public function save()
	{
		$this->_mapper->save($this);
		return $this;
	}

	/**
	 * Zwraca tekstową reprezentację modelu
	 */
	public function __toString()
	{
		return $this->id;
	}

}