<?php
class Centixx_Model_DbTable_Timesheet extends Centixx_Db_Table_Abstract
{
    protected $_name = 'timesheets';
    protected $_identity = 'timesheet_id';
    protected $_defaultOrder = 'timesheet_date ASC';
    /*
	protected $_dependentTables = array('Centixx_Model_DbTable_Group');
    protected $_referenceMap    = array(
        'Group' => array(
            'columns'           => 'user_group',
            'refTableClass'     => 'Centixx_Model_DbTable_Group',
            'refColumns'        => 'group_id'
        ),
    );
    */
}
