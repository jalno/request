<?php
namespace packages\request\process;
use packages\base\db\dbObject;

class param extends dbObject{
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
