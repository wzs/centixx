<?php
class Centixx_Model_DbTable_User extends Centixx_Db_Table_Abstract
{
    protected $_name = 'users';
    protected $_identity = 'user_id';
    protected $_defaultOrder = 'user_surname ASC';
	protected $_dependentTables = array('Centixx_Model_DbTable_Group');
    protected $_referenceMap    = array(
        'Group' => array(
            'columns'           => 'user_group',
            'refTableClass'     => 'Centixx_Model_DbTable_Group',
            'refColumns'        => 'group_id'
        ),
    );
}