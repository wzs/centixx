<?php
class Centixx_Model_DbTable_Project extends Centixx_Db_Table_Abstract
{
    protected $_name = 'projects';
    protected $_identity = 'project_id';
    protected $_dependentTables = array('Centixx_Model_DbTable_Group');
    protected $_referenceMap    = array(
        'Manager' => array(
            'columns'           => 'project_manager',
            'refTableClass'     => 'Centixx_Model_DbTable_Users',
            'refColumns'        => 'user_id'
        ),
    );
}