<?php
abstract class Centixx_Db_Table_Abstract extends Zend_Db_Table_Abstract
{
	/**
	 * @var string wg. jakiego pola mają być sortowane wiersze. Można dodać też ASC lub DESC
	 */
	protected $_defaultOrder = null;

	public function getOrder()
	{
		return $this->_defaultOrder != null ? $this->_defaultOrder : $this->_identity . ' ASC';
	}

	/**
	 * (non-PHPdoc)
	 * @see library/Zend/Db/Table/Zend_Db_Table_Abstract::fetchAll()
	 */
    public function fetchAll($where = null, $order = null, $count = null, $offset = null)
    {
        if (!($where instanceof Zend_Db_Table_Select)) {
            $select = $this->select();

            if ($where !== null) {
                $this->_where($select, $where);
            }

        	if ($order == null) {
				$order = $this->getOrder();
			}
			$this->_order($select, $order);

            if ($count !== null || $offset !== null) {
                $select->limit($count, $offset);
            }

        } else {
            $select = $where;
        }
        return parent::fetchAll($select, $order, $count, $offset);
    }
}
