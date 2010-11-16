<?php
abstract class Centixx_Model_Mapper_Abstract
{
	const CLASS_PATH_SEPARATOR = '_';
	const CLASS_PREFIX = 'Centixx_Model_';

	/**
	 * Pełna nazwa klasy ustawianej jako $_dbTable
	 * Ustawienie tego parametru powoduje nadpisanie domyslnego zachowania jakim
	 * jest wydobywanie z nazwy bieżącej klasy
	 * @var string
	 */
	protected $_dbTableClassName = null;

	/**
	 * @var Zend_Db_Table_Abstract
	 */
	protected $_dbTable = null;

	/**
	 * Klucz główny mapowanej tabeli
	 * @var string
	 */
	protected $_dbPrimaryKey = null;

	/**
	 * Set Table Gateway
	 * @param string|Zend_Db_Table_Abstract $table
	 * @throws InvalidArgumentException
	 */
	public function setDbTable($table)
	{
		if (is_string($table)) {
			$table = new $table();
		}

		if (!$table instanceof Zend_Db_Table_Abstract) {
			throw new InvalidArgumentException('Invalid Table Gateway Argument provided');
		}

		$this->_dbTable = $table;
		return $this;
	}

	/**
	 * @return Zend_Db_Table_Abstract
	 */
	public function getDbTable()
	{

		if ($this->_dbTable == null) {
			$this->setDbTable($this->_getDefaultDbTableClass());
		}
		return $this->_dbTable;
	}

	/**
	 * @return string
	 */
	protected function _getDefaultDbTableClass()
	{
		if (!is_null($this->_dbTableClassName)) {
			return $this->_dbTableClassName;
		}

		$parts = explode(self::CLASS_PATH_SEPARATOR, get_class($this));
		$parts[count($parts) - 2] = 'DbTable';
		return join(self::CLASS_PATH_SEPARATOR, $parts);
	}

	/**
	 *
	 * Tworzy nowy obiekt mapowanej klasy i jesli podany zostanie parametr $row
	 * wypełnia go danymi
	 * @param Zend_Db_Table_Row_Abstract|null $row
	 * @return Centixx_Model_Abstract
	 */
	protected function _getNewModelInstance(Zend_Db_Table_Row_Abstract $row = null, $classNameShort = null)
	{
		if ($classNameShort) {
			$className = self::CLASS_PREFIX . $classNameShort;
		} else {
			$className = str_replace('Mapper_', '', get_class($this));
		}

		$model = new $className();
		$model->setMapper($this);
		if ($row != null) {
			$this->fillModel($model, $row);
		}
		return $model;
	}

	/**
	 * Zwraca listę instacji wszystkich mapowanych obiektow
	 * @param array $where
	 * @return array<Centixx_Model_Abstract>
	 */
	public function fetchAll($query = null, $order = null)
	{
		if ($order == null) {
			$order = $this->getDbTable()->getOrder();
		}
		$resultSet = $this->getDbTable()->fetchAll($query, $order);
		$entries = array();
		foreach ($resultSet as $row) {
			$entries[] = $this->_getNewModelInstance($row);
		}
		return $entries;
	}

	/**
	 * Zwraca mapowany model na podstawie klucza glownego
	 * @param int $id
	 * @return Centixx_Model_Abstract
	 */
	public function find($id)
	{
		return $this->findByField($id, $this->_getPrimaryKey());
	}

	/**
	 * @return string klucz główny mapowanej tabeli
	 */
	protected function _getPrimaryKey()
	{
		if ($this->_dbPrimaryKey == null) {
			$this->_dbPrimaryKey = array_pop($this->getDbTable()->info(Zend_Db_Table_Abstract::PRIMARY));
		}
		return $this->_dbPrimaryKey;
	}

	/**
	 * Zwraca mapowany model na podstawie podanego pola
	 * @param mixed $value
	 * @param string $field
	 * @return Centixx_Model_Abstract
	 */
	public function findByField($value, $field)
	{
		$table = $this->getDbTable();
		$query = $table->select()->where($table->getAdapter()->quoteIdentifier($field) . ' = ?', $value);
		$result = $table->fetchAll($query);
		if (0 == count($result)) {
			return null;
		}
		return $this->_getNewModelInstance($result->current());
	}

	abstract public function save(Centixx_Model_Abstract $model);
	abstract protected function fillModel(Centixx_Model_Abstract $model, Zend_Db_Table_Row_Abstract $row);

	/**
	 * @param int $id identyfikator szukanego obiektu
	 * @param string $classNameShort krótka nazwa klasy szukanego obiektu
	 * @return Centixx_Model_Abstract szukany obiekt
	 * @throws Centixx_Model_Exception
	 */
	protected function getObject($id, $classNameShort)
	{
		if (ctype_digit($id)) {
			$mapper = $this->_getClassMaper($classNameShort);
			if ($object = $mapper->find($id)) {
				return $object;
			}
		}
		throw new Centixx_Model_Exception("Invalid property for " . $classNameShort);
	}

	/**
	 * Zwraca instancję klasy mappera na podstawie podanej nazwy
	 * @param string $classNameShort
	 * @return Centixx_Model_Mapper_Abstract
	 */
	protected function _getClassMaper($classNameShort)
	{
		$mapperClassName = self::CLASS_PREFIX . 'Mapper' . self::CLASS_PATH_SEPARATOR . ucfirst($classNameShort);
		if (!class_exists($mapperClassName)) {
			throw new Centixx_Model_Exception("Cannot find class " . $mapperClassName);
		}

		return new $mapperClassName();
	}

	/**
	 * Zwraca i ładuje powiązany z modelem obiekt
	 * @param Centixx_Model_Abstract $model
	 * @param string $field nazwa żądanego pola
	 * @param string $mapperClassName krótka nazwa mappera
	 * @return Centixx_Model_Abstract
	 */
	public function getRelated(Centixx_Model_Abstract $model, $field, $mapperClassName)
	{

		$getMethod = 'get' . ucfirst($field);
		$setMethod = 'set' . ucfirst($field);

		$value = call_user_method_array($getMethod, $model, array(true));

		if (!$value instanceof Centixx_Model_Abstract && $value != null) {
			$mapper = $this->_getClassMaper($mapperClassName);
			$value = $mapper->find($value);
			call_user_method($setMethod, $model, $value);
		}
		return $value;
	}

	/**
	 * Zwraca i ładuje powiązane z modelem obiekty
	 * @param Centixx_Model_Abstract $model
	 * @param string $field nazwa żądanego pola
	 * @param string $mapperClassName krótka nazwa mappera
	 * @param array $where - argument metody Zend_Db_Query::where() jesli ma byc zwracany zbior
	 * @return array<Centixx_Model_Abstract>
	 */
	public function getRelatedSet(Centixx_Model_Abstract $model, $field, $mapperClassName, $where)
	{
		$getMethod = 'get' . ucfirst($field);
		$setMethod = 'set' . ucfirst($field);

		$value = call_user_method_array($getMethod, $model, array(true));
		if (!$value instanceof Centixx_Model_Abstract) {
			$mapper = $this->_getClassMaper($mapperClassName);
			$table = $mapper->getDbTable();
			$query = $table->select()->where($where[0], $where[1])->order($table->getOrder());
			$value = $mapper->fetchAll($query);
			call_user_method($setMethod, $model, $value);
		}
		return $value;
	}


	/**
	 * Zwraca identyfikator danego parametru
	 * Rozpoznaje, czy jest to juz zainicjowany obiekt,
	 * czy tylko jego id (na potrzeby lazy load).
	 *
	 * @param int|Centixx_Model_Abstract $property
	 */
	protected function _findId($property)
	{
		return ($property instanceof Centixx_Model_Abstract) ? $property->id : $property;
	}
}