<?php
namespace packages\request\exceptions;
class Exception extends \Exception{
    protected $data;
    public function __construct(array $data, string $message = ''){
        $this->data = $data;
        $this->message = $message;
    }
    public function getData():array{
        return $this->data;
    }
}
class typeException extends Exception{
    public function __construct(array $data){
        parent::__construct($data, "cannot find the class");
    }
}