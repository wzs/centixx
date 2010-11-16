<?php
class Centixx_Model_DbTable_Group extends Centixx_Db_Table_Abstract
{
    protected $_name = 'groups';
    protected $_identity = 'group_id';
    protected $_defaultOrder = 'group_name ASC';
    protected $_dependentTables = array('Centixx_Model_DbTable_User');

    protected $_referenceMap    = array(
        'Manager' => array(
            'columns'           => 'group_manager',
            'refTableClass'     => 'Centixx_Model_DbTable_User',
            'refColumns'        => 'user_id'
        ),
    );
}