<?php

namespace packages\request\Process;

use packages\base\DB\DBObject;

class Param extends DBObject
{
    protected $dbTable = 'request_processes_params';
    protected $apiclass;
    protected $primaryKey = 'id';
    protected $dbFields = [
        'process' => ['type' => 'int', 'required' => true],
        'name' => ['type' => 'text', 'required' => true],
        'value' => ['type' => 'text', 'required' => true],
    ];
    protected $jsonFields = ['value'];
}
