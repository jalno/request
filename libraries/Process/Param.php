<?php
namespace packages\request\Process;
use packages\base\DB\DBObject;

class Param extends DBObject{
	protected $dbTable = "request_processes_params";
	protected $apiclass;
	protected $primaryKey = "id";
	protected $dbFields = array(
        'process' => array('type' => 'int', 'required' => true),
		'name' => array('type' => 'text', 'required' => true),
		'value' => array('type' => 'text', 'required' => true),
    );
	protected $jsonFields = array('value');
}
