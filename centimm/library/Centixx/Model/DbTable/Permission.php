<?php
class Centixx_Model_DbTable_Permission extends Centixx_Db_Table_Abstract
{
    protected $_name = 'permissions';
    protected $_identity = 'permission_id';

    protected $_referenceMap    = array(
        'From' => array(
            'columns'           => 'permission_from',
            'refTableClass'     => 'Centixx_Model_DbTable_User',
            'refColumns'        => 'user_id'
        ),
        'For' => array(
            'columns'           => 'permission_to',
            'refTableClass'     => 'Centixx_Model_DbTable_User',
            'refColumns'        => 'user_id'
        ),

    );
}