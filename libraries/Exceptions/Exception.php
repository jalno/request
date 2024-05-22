<?php
namespace packages\request\Exceptions;
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
