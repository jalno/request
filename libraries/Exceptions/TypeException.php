<?php
namespace packages\request\Exceptions;

class TypeException extends Exception{
    public function __construct(array $data){
        parent::__construct($data, "cannot find the class");
    }
}